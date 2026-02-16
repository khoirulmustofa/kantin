<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = \App\Models\Product::with(['category', 'images'])->where('is_active', true);

            // Filter by Category
            if ($request->category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            // Search
            if ($request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $products = $query->latest()->paginate(12)->withQueryString();
            $categories = \App\Models\ProductCategory::withCount('products')->get();

            return Inertia::render('Front/Product/Index', [
                'products' => $products,
                'categories' => $categories,
                'filters' => $request->only(['category', 'search']),
                'menu' => 'products',
                'title' => 'Product List',
            ])->rootView('front');
        } catch (\Throwable $th) {
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function show($slug)
    {
        try {
            $product = \App\Models\Product::with(['category', 'images'])
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            // Related Products
            $relatedProducts = \App\Models\Product::with(['category', 'images'])
                ->where('product_category_id', $product->product_category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->latest()
                ->take(6)
                ->get();

            return Inertia::render('Front/Product/Show', [
                'product' => $product,
                'related_products' => $relatedProducts,
                'menu' => 'products',
                'title' => $product->name,
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
