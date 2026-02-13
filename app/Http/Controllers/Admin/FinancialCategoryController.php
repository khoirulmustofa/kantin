<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = FinancialCategory::query();

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
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

            return Inertia::render('Admin/FinancialCategory/Index', [
                'menu' => 'financial_categories',
                'title' => 'Financial Categories',
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
            'name' => 'required|string|max:255|unique:financial_categories',
            'type' => 'required|in:income,expense',
        ]);

        try {
            FinancialCategory::create($validated);
            return redirect()->back()->with('success', 'Financial Category created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create financial category: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $category = FinancialCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:financial_categories,name,' . $category->id,
            'type' => 'required|in:income,expense',
        ]);

        try {
            $category->update($validated);
            return redirect()->back()->with('success', 'Financial Category updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update financial category: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = FinancialCategory::findOrFail($id);
            $category->delete();
            return redirect()->back()->with('success', 'Financial Category deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete financial category: ' . $th->getMessage());
        }
    }
}
