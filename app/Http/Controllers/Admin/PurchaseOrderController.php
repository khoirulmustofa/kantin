<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = PurchaseOrder::with(['supplier', 'user', 'financialAccount', 'purchaseOrderItems.product']);

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where('po_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
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

            $purchaseOrders = $query->paginate($rows)->withQueryString();

            return Inertia::render('Admin/PurchaseOrder/Index', [
                'menu' => 'purchase-orders',
                'title' => 'Purchase Order Management',
                'purchaseOrders' => $purchaseOrders,
                'financialAccounts' => FinancialAccount::where('is_active', true)->get(),
                'filters' => $request->only(['search', 'rows', 'multiSortMeta']),
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Admin/PurchaseOrder/Form', [
            'menu' => 'purchase-orders',
            'title' => 'Create Purchase Order',
            'suppliers' => Supplier::where('is_active', true)->get(),
            'products' => Product::where('is_active', true)->get(),
            'financialAccounts' => FinancialAccount::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|uuid|exists:suppliers,id',
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'shipping_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,ordered,received,cancelled',
            'payment_status' => 'required|in:unpaid,paid,failed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $grandTotal = collect($validated['items'])->sum(function ($item) {
                return $item['cost_price'] * $item['quantity'];
            }) + $validated['shipping_cost'];

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePoNumber(),
                'supplier_id' => $validated['supplier_id'],
                'financial_account_id' => $validated['financial_account_id'],
                'user_id' => Auth::user()->id,
                'shipping_cost' => $validated['shipping_cost'],
                'notes' => $validated['notes'],
                'grand_total' => $grandTotal,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase Order created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create purchase order: ' . $th->getMessage());
        }
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'financialAccount', 'purchaseOrderItems.product.category', 'user'])->findOrFail($id);
        return Inertia::render('Admin/PurchaseOrder/Show', [
            'menu' => 'purchase-orders',
            'title' => 'Purchase Order Detail',
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'financialAccount', 'purchaseOrderItems.product'])->findOrFail($id);
        return Inertia::render('Admin/PurchaseOrder/Form', [
            'menu' => 'purchase-orders',
            'title' => 'Edit Purchase Order '.$purchaseOrder->po_number,
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => Supplier::where('is_active', true)->get(),
            'products' => Product::where('is_active', true)->get(),
            'financialAccounts' => FinancialAccount::where('is_active', true)->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $validated = $request->validate([
            'supplier_id' => 'required|uuid|exists:suppliers,id',
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'shipping_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,ordered,received,cancelled',
            'payment_status' => 'required|in:unpaid,paid,failed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $grandTotal = collect($validated['items'])->sum(function ($item) {
                return $item['cost_price'] * $item['quantity'];
            }) + $validated['shipping_cost'];

            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'financial_account_id' => $validated['financial_account_id'],
                'user_id' => Auth::user()->id,
                'shipping_cost' => $validated['shipping_cost'],
                'notes' => $validated['notes'],
                'grand_total' => $grandTotal,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
            ]);

            $purchaseOrder->purchaseOrderItems()->delete();
            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase Order updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update purchase order: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);
            $purchaseOrder->delete();
            return redirect()->back()->with('success', 'Purchase Order deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete purchase order: ' . $th->getMessage());
        }
    }

    public function syncToFinance($id)
    {
        try {
            DB::beginTransaction();
            $purchaseOrder = PurchaseOrder::with('mutation')->findOrFail($id);

            if ($purchaseOrder->mutation) {
                return redirect()->back()->with('error', 'Purchase Order already synced to finance.');
            }

            if ($purchaseOrder->payment_status !== 'paid') {
                return redirect()->back()->with('error', 'Only paid purchase orders can be synced to finance.');
            }

            // Get associated account and category for expense
            $account = $purchaseOrder->financialAccount;
            $category = \App\Models\FinancialCategory::where('type', 'expense')->where('name', 'Belanja Kantin')->first()
                ?? \App\Models\FinancialCategory::where('type', 'expense')->first();

            if (!$account || !$category) {
                return redirect()->back()->with('error', 'Selected Financial Account or Expense Category not found.');
            }

            $mutation = \App\Models\FinancialMutation::create([
                'financial_account_id' => $account->id,
                'financial_category_id' => $category->id,
                'flow' => 'out',
                'amount' => $purchaseOrder->grand_total,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => "Belanja PO: {$purchaseOrder->po_number}",
                'reference_type' => get_class($purchaseOrder),
                'reference_id' => $purchaseOrder->id,
            ]);

            // Update Account Balance (decrement for expense)
            $account->decrement('balance', $mutation->amount);

            DB::commit();
            return redirect()->back()->with('success', 'Purchase Order synced to finance successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to sync: ' . $th->getMessage());
        }
    }

    private function generatePoNumber()
    {
        $prefix = 'PO';
        $date = Carbon::now()->format('Ymd');
        $count = PurchaseOrder::withTrashed()->count();
        $sequence = $count + 1;

        $poNumber = "{$prefix}-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        while (PurchaseOrder::where('po_number', $poNumber)->exists()) {
            $sequence++;
            $poNumber = "{$prefix}-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        }

        return $poNumber;
    }
}
