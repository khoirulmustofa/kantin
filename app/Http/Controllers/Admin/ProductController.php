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
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
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
                'slug' => $this->slugName($validated['name']),
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
            $oldName = $product->name;
            $newName = $validated['name'];
            $product->update([
                'name' => $newName,
                'slug' => ($newName !== $oldName)
                    ? $this->slugName($newName)
                    : $product->slug,
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

    public function duplicate($id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $product = Product::with('images')->findOrFail($id);

            // Create new name with " (copy)"
            $newName = $product->name . ' (copy)';
            // Ensure uniqueness if multiple copies are made
            $count = 1;
            while (Product::where('name', $newName)->exists()) {
                $newName = $product->name . ' (copy ' . $count . ')';
                $count++;
            }

            $newProduct = $product->replicate();
            $newProduct->name = $newName;
            $newProduct->slug = $this->slugName($newName);
            $newProduct->save();

            // Duplicate images
            foreach ($product->images as $image) {
                if ($image->image) {
                    $oldPath = $image->image;
                    $filename = basename($oldPath);
                    $newPath = 'products/' . \Illuminate\Support\Str::uuid() . '_' . $filename;

                    \Illuminate\Support\Facades\Storage::disk('public')->copy($oldPath, $newPath);

                    $newProduct->images()->create([
                        'image' => $newPath,
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Product duplicated successfully.');
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Failed to duplicate product: ' . $th->getMessage());
        }
    }

    private function slugName($name)
    {
        $slugBase = \Illuminate\Support\Str::slug($name);

        // Coba buat slug unik
        do {
            // Generate 5 digit acak angka dan huruf kecil
            $randomCode = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(5));
            $finalSlug = $slugBase . '-' . $randomCode;

            // Cek apakah slug ini sudah ada di tabel products
            $exists = \App\Models\Product::where('slug', $finalSlug)->exists();
        } while ($exists); // Jika ada yang sama, ulangi generate

        return $finalSlug;
    }
}
