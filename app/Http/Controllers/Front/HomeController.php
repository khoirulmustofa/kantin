<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $latestProducts = Product::with(['category', 'images'])
                ->where('is_active', true)
                ->latest()
                ->take(8)
                ->get();

            $categories = ProductCategory::withCount('products')
                ->get();

            return Inertia::render('Front/Home/Index', [
                'menu' => 'home',
                'title' => 'Home',
                'products' => $latestProducts,
                'categories' => $categories,
            ])->rootView('front');
        } catch (\Throwable $th) {
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
