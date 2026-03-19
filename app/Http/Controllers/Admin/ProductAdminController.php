<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->withTrashed();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }
        if ($request->category) $query->where('category_id', $request->category);
        if ($request->brand)    $query->where('brand_id', $request->brand);
        if ($request->status === 'active')   $query->where('is_active', true);
        if ($request->status === 'inactive') $query->where('is_active', false);

        $products   = $query->latest()->paginate(20);
        $categories = Category::all();
        $brands     = Brand::all();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands     = Brand::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $data['slug'] = Str::slug($request->name) . '-' . uniqid();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->saveImage(
                $request->file('thumbnail'), 'products/thumbnails', 400, 400
            );
        }

        $product = Product::create($data);

        // Handle color-tagged images
        $this->saveColorImages($request, $product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product "' . $product->name . '" created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variants']);
        $categories = Category::where('is_active', true)->get();
        $brands     = Brand::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $this->saveImage(
                $request->file('thumbnail'), 'products/thumbnails', 400, 400
            );
        }

        $product->update($data);

        // Handle color-tagged images
        $this->saveColorImages($request, $product);

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return response()->json(['active' => $product->is_active]);
    }

    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'color'    => 'nullable|string|max:50',
        ]);

        $count = $product->images()->count();
        $saved = [];

        foreach ($request->file('images') as $i => $image) {
            $img = ProductImage::create([
                'product_id' => $product->id,
                'image'      => $this->saveImage($image, 'products', 800, 800),
                'color'      => $request->color ?: null,
                'sort_order' => $count + $i,
                'is_primary' => $count === 0 && $i === 0,
            ]);
            $saved[] = [
                'id'      => $img->id,
                'url'     => Storage::url($img->image),
                'color'   => $img->color,
                'primary' => $img->is_primary,
            ];
        }

        return response()->json(['message' => 'Images uploaded.', 'images' => $saved]);
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image);
        $image->delete();
        return response()->json(['message' => 'Image deleted.']);
    }

    public function storeVariant(Request $request, Product $product)
    {
        $data = $request->validate([
            'color'   => 'nullable|string|max:50',
            'storage' => 'nullable|string|max:50',
            'ram'     => 'nullable|string|max:50',
            'price'   => 'required|numeric|min:0',
            'stock'   => 'required|integer|min:0',
            'sku'     => 'required|string|unique:product_variants,sku',
        ]);
        $product->variants()->create($data);
        return back()->with('success', 'Variant added.');
    }

    public function deleteVariant(ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Save color-tagged image groups from the request.
     * Expects:
     *   color_images[0][color]    = 'Black'
     *   color_images[0][files][]  = <uploaded files>
     *   general_images[]          = <uploaded files with no color>
     */
    private function saveColorImages(Request $request, Product $product): void
    {
        $count = $product->images()->count();

        // ── General images (no color tag) ────────────────────────
        // Use allFiles() to reliably get multiple uploaded files
        $generalFiles = $request->allFiles()['general_images'] ?? [];

        // allFiles() may return a single UploadedFile instead of array
        // when only one file is uploaded — normalise to array
        if (!is_array($generalFiles)) {
            $generalFiles = [$generalFiles];
        }

        foreach ($generalFiles as $i => $file) {
            if (!$file || !$file->isValid()) continue;

            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $this->saveImage($file, 'products', 800, 800),
                'color'      => null,
                'sort_order' => $count,
                'is_primary' => $count === 0,
            ]);
            $count++;
        }

        // ── Color-tagged image groups ─────────────────────────────
        $allFiles    = $request->allFiles();
        $colorImages = $allFiles['color_images'] ?? [];

        foreach ($colorImages as $index => $group) {
            $color = $request->input("color_images.{$index}.color");
            $files = $group['files'] ?? [];

            if (empty($color)) continue;

            // Normalise single file to array
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) continue;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $this->saveImage($file, 'products', 800, 800),
                    'color'      => $color,
                    'sort_order' => $count,
                    'is_primary' => false,
                ]);
                $count++;
            }
        }
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'required|exists:brands,id',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'sku'               => 'required|string|unique:products,sku,' . $ignoreId,
            'stock'             => 'required|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'display_size'      => 'nullable|string|max:50',
            'display_type'      => 'nullable|string|max:100',
            'processor'         => 'nullable|string|max:100',
            'ram'               => 'nullable|string|max:50',
            'storage'           => 'nullable|string|max:50',
            'battery'           => 'nullable|string|max:50',
            'camera_main'       => 'nullable|string|max:100',
            'camera_front'      => 'nullable|string|max:100',
            'os'                => 'nullable|string|max:100',
            'network'           => 'nullable|string|max:50',
            'colors'            => 'nullable|array',
            'colors.*'          => 'string|max:50',
            'is_featured'       => 'nullable',
            'is_active'         => 'nullable',
            'thumbnail'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'general_images'    => 'nullable|array',
            'general_images.*'  => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]) + [
            'is_active'   => $request->has('is_active')   ? true : false,
            'is_featured' => $request->has('is_featured') ? true : false,
        ];
    }

    private function saveImage($file, string $folder, int $w, int $h): string
    {
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = "{$folder}/{$filename}";

        // Use Intervention Image v3 if available, else store original
        if (class_exists(\Intervention\Image\Facades\Image::class)) {
            $img = Image::make($file)
        ->fit($w, $h)
        ->encode('webp', 85);

            $path = "{$folder}/" . uniqid() . '_' . time() . '.webp';
            Storage::disk('public')->put($path, $img);
        } else {
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
        }

        return $path;
    }
}
