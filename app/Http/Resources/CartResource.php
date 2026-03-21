<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        // Pick color-specific image
        $images = $this->product->images ?? collect();
        $img = null;
        if ($this->selected_color) {
            $img = $images->firstWhere('color', $this->selected_color);
        }
        if (!$img) {
            $img = $images->whereNull('color')->first();
        }
        $imageUrl = $img
            ? url('storage/' . $img->image)
            : ($this->product->thumbnail ? url('storage/' . $this->product->thumbnail) : null);

        return [
            'id'             => $this->id,
            'product_id'     => $this->product_id,
            'product_name'   => $this->product->name,
            'product_slug'   => $this->product->slug,
            'thumbnail'      => $imageUrl,
            'variant_id'     => $this->variant_id,
            'variant'        => $this->variant ? [
                'id'      => $this->variant->id,
                'label'   => $this->variant->getDetailsLabel(),
                'ram'     => $this->variant->ram,
                'storage' => $this->variant->storage,
            ] : null,
            'selected_color' => $this->selected_color,
            'quantity'       => $this->quantity,
            'unit_price'     => (float) ($this->variant ? $this->variant->price : $this->product->getCurrentPrice()),
            'subtotal'       => (float) $this->getSubtotal(),
            'exchange_data'  => $this->exchange_data,
        ];
    }
}
