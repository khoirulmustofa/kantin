<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\FinancialCategory;
use App\Models\FinancialMutation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialTransactionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = FinancialMutation::with(['account', 'category', 'reference']);

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

            // Handle Flow Filter
            if ($request->flow) {
                $query->where('flow', $request->flow);
            }

            // Handle Account Filter
            if ($request->account_id) {
                $query->where('financial_account_id', $request->account_id);
            }

            // Handle Multi Sort
            $multiSortMeta = $request->input('multiSortMeta');
            if ($multiSortMeta) {
                $sorts = json_decode($multiSortMeta, true);
                if (is_array($sorts)) {
                    foreach ($sorts as $sort) {
                        $direction = ($sort['order'] ?? 1) === 1 ? 'asc' : 'desc';
                        $field = $sort['field'] ?? 'transaction_date';

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
                $query->latest('transaction_date')->latest('created_at');
            }

            $mutations = $query->paginate($rows)->withQueryString();

            $mutations->getCollection()->transform(function ($mutation) {
                $mutation->proof_image_url = $mutation->proof_image ? asset('storage/' . $mutation->proof_image) : null;
                return $mutation;
            });

            return Inertia::render('Admin/FinancialTransaction/Index', [
                'menu' => 'financial_transactions',
                'title' => 'Financial Transactions',
                'mutations' => $mutations,
                'accounts' => FinancialAccount::all(),
                'filters' => $request->only(['search', 'rows', 'multiSortMeta', 'flow', 'account_id']),
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
