<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\FinancialCategory;
use App\Models\FinancialMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FinancialCashInController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = FinancialMutation::with(['account', 'category'])
                ->where('flow', 'in');

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhereHas('account', function ($aq) use ($search) {
                            $aq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('category', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            });

            // Handle Multi Sort
            $multiSortMeta = $request->input('multiSortMeta');
            if ($multiSortMeta) {
                $sorts = json_decode($multiSortMeta, true);
                if (is_array($sorts)) {
                    foreach ($sorts as $sort) {
                        $direction = ($sort['order'] ?? 1) === 1 ? 'asc' : 'desc';
                        $field = $sort['field'] ?? 'transaction_date';

                        // Handle relationship sorting mapping if needed
                        if ($field === 'account.name') {
                            $query->join('financial_accounts', 'financial_mutations.financial_account_id', '=', 'financial_accounts.id')
                                ->orderBy('financial_accounts.name', $direction)
                                ->select('financial_mutations.*');
                        } elseif ($field === 'category.name') {
                            $query->join('financial_categories', 'financial_mutations.financial_category_id', '=', 'financial_categories.id')
                                ->orderBy('financial_categories.name', $direction)
                                ->select('financial_mutations.*');
                        } else {
                            $query->orderBy($field, $direction);
                        }
                    }
                }
            } else {
                $query->latest('transaction_date');
            }

            $mutations = $query->paginate($rows)->withQueryString();

            // Transform URLs for proof images
            $mutations->getCollection()->transform(function ($mutation) {
                $mutation->proof_image_url = $mutation->proof_image ? asset('storage/' . $mutation->proof_image) : null;
                return $mutation;
            });

            return Inertia::render('Admin/FinancialCashIn/Index', [
                'menu' => 'financial-cash-in',
                'title' => 'Cash Inflow',
                'mutations' => $mutations,
                'accounts' => FinancialAccount::where('is_active', true)->get(),
                'categories' => FinancialCategory::where('type', 'income')->get(),
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
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'financial_category_id' => 'required|uuid|exists:financial_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $proofImagePath = null;
            if ($request->hasFile('proof_image')) {
                $proofImagePath = $request->file('proof_image')->store('mutations/inflow', 'public');
            }

            $mutation = FinancialMutation::create([
                'financial_account_id' => $validated['financial_account_id'],
                'financial_category_id' => $validated['financial_category_id'],
                'flow' => 'in',
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
                'proof_image' => $proofImagePath,
            ]);

            // Update Account Balance
            $account = $mutation->account;
            $account->increment('balance', $mutation->amount);

            DB::commit();
            return redirect()->back()->with('success', 'Cash inflow recorded successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record inflow: ' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $mutation = FinancialMutation::findOrFail($id);

        $validated = $request->validate([
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'financial_category_id' => 'required|uuid|exists:financial_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Reverse old balance impact
            $oldAccount = $mutation->account;
            $oldAccount->decrement('balance', $mutation->amount);

            $proofImagePath = $mutation->proof_image;
            if ($request->hasFile('proof_image')) {
                if ($mutation->proof_image) {
                    Storage::disk('public')->delete($mutation->proof_image);
                }
                $proofImagePath = $request->file('proof_image')->store('mutations/inflow', 'public');
            }

            $mutation->update([
                'financial_account_id' => $validated['financial_account_id'],
                'financial_category_id' => $validated['financial_category_id'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
                'proof_image' => $proofImagePath,
            ]);

            // Apply new balance impact
            $newAccount = FinancialAccount::find($validated['financial_account_id']);
            $newAccount->increment('balance', $validated['amount']);

            DB::commit();
            return redirect()->back()->with('success', 'Cash inflow updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update inflow: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $mutation = FinancialMutation::findOrFail($id);

            // Reverse balance impact
            $account = $mutation->account;
            $account->decrement('balance', $mutation->amount);

            if ($mutation->proof_image) {
                Storage::disk('public')->delete($mutation->proof_image);
            }

            $mutation->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Cash inflow deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete inflow: ' . $th->getMessage());
        }
    }
}
