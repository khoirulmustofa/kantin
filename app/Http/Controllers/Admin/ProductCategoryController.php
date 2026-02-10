<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = ProductCategory::query();

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
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

            $categories = $query->paginate($rows)->withQueryString();

            // Map images to include public URLs for the frontend
            $categories->getCollection()->transform(function ($category) {
                $category->image_url = $category->image ? asset('storage/' . $category->image) : null;
                return $category;
            });

            return Inertia::render('Admin/ProductCategory/Index', [
                'menu' => 'product-categories',
                'title' => 'Product Categories',
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
            'name' => 'required|string|max:255|unique:product_categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('product_categories', 'public');
            }

            ProductCategory::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'image' => $imagePath,
            ]);

            return redirect()->back()->with('success', 'Category created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create category: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $imagePath = $category->image;
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
                }
                $imagePath = $request->file('image')->store('product_categories', 'public');
            }

            $category->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'image' => $imagePath,
            ]);

            return redirect()->back()->with('success', 'Category updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update category: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = ProductCategory::findOrFail($id);

            // Delete image if exists
            if ($category->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return redirect()->back()->with('success', 'Category deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete category: ' . $th->getMessage());
        }
    }
}
