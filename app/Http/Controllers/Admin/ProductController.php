<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = Product::with(['category', 'images']);

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });

            // Handle Multi Sort
            $multiSortMeta = $request->input('multiSortMeta');
            if ($multiSortMeta) {
                $sorts = json_decode($multiSortMeta, true);
                if (is_array($sorts)) {
                    foreach ($sorts as $sort) {
                        $direction = ($sort['order'] ?? 1) === 1 ? 'asc' : 'desc';
                        $field = $sort['field'] ?? 'created_at';
                        $query->orderBy($field, $direction);
                    }
                }
            } else {
                $query->latest();
            }

            $products = $query->paginate($rows)->withQueryString();

            // Map images to include public URLs for the frontend
            $products->getCollection()->transform(function ($product) {
                $product->images->transform(function ($image) {
                    $image->image_url = $image->image ? asset('storage/' . $image->image) : null;
                    return $image;
                });
                return $product;
            });

            $categories = ProductCategory::orderBy('name', 'asc')->get();

            // Map category images too
            $categories->transform(function ($category) {
                $category->image_url = $category->image ? asset('storage/' . $category->image) : null;
                return $category;
            });

            return Inertia::render('Admin/Product/Index', [
                'menu' => 'products',
                'title' => 'Product Management',
                'products' => $products,
                'categories' => $categories,
                'filters' => $request->only(['search', 'rows', 'multiSortMeta']),
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'product_category_id' => 'required|uuid|exists:product_categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? '',
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'is_active' => $validated['is_active'],
                'product_category_id' => $validated['product_category_id'],
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Product created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create product: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'product_category_id' => 'required|uuid|exists:product_categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_images' => 'nullable|array',
        ]);

        try {
            $product->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? '',
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'stock' => $validated['stock'],
                'is_active' => $validated['is_active'],
                'product_category_id' => $validated['product_category_id'],
            ]);

            // Handle deleted images
            $deletedImages = $request->input('deleted_images', []);
            if (is_array($deletedImages) && count($deletedImages) > 0) {
                foreach ($deletedImages as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        if ($image->image) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image);
                        }
                        $image->delete();
                    }
                }
            }

            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->images()->create([
                        'image' => $path,
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Product updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update product: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::with('images')->findOrFail($id);

            foreach ($product->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image);
                $image->delete();
            }

            $product->delete();

            return redirect()->back()->with('success', 'Product deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete product: ' . $th->getMessage());
        }
    }
}
