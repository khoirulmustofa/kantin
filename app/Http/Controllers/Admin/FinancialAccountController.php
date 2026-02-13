<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialAccountController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = FinancialAccount::query();

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
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

            $accounts = $query->paginate($rows)->withQueryString();

            return Inertia::render('Admin/FinancialAccount/Index', [
                'menu' => 'financial_accounts',
                'title' => 'Financial Accounts',
                'accounts' => $accounts,
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
            'name' => 'required|string|max:255|unique:financial_accounts',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);

        try {
            FinancialAccount::create($validated);
            return redirect()->back()->with('success', 'Financial Account created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create financial account: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $account = FinancialAccount::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:financial_accounts,name,' . $account->id,
            'account_number' => 'nullable|string|max:255',
            'balance' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);

        try {
            $account->update($validated);
            return redirect()->back()->with('success', 'Financial Account updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update financial account: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $account = FinancialAccount::findOrFail($id);
            $account->delete();
            return redirect()->back()->with('success', 'Financial Account deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete financial account: ' . $th->getMessage());
        }
    }
}
