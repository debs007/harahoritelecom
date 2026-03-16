<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        //return response("Hola amigo",200);

        $featuredProducts = Product::with(['brand', 'images'])
            ->active()->featured()->latest()->take(8)->get();

        $newArrivals = Product::with(['brand', 'images'])
            ->active()->latest()->take(8)->get();

        $topRated = Product::with(['brand', 'images'])
            ->active()->orderBy('avg_rating', 'desc')->take(8)->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->get();

        $brands = Brand::where('is_active', true)->take(10)->get();

        $bannerProducts = Product::with(['images'])
            ->active()->featured()->take(4)->get();

        return view('frontend.home.index', compact(
            'featuredProducts',
            'newArrivals',
            'topRated',
            'categories',
            'brands',
            'bannerProducts'
        ));
    }
}
