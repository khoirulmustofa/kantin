<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $rows = $request->input('rows', 10);
            $query = Order::select('orders.*') // Hindari kolom ID tertimpa
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->with(['user', 'financialAccount', 'orderItems.product', 'mutation']);

            // Handle Search
            $query->when($request->search, function ($q, $search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
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
                        // Mapping field jika dari PrimeVue namanya 'user.name'
                        if ($field === 'user.name') {
                            $query->orderBy('users.name', $direction);
                        } else {
                            $query->orderBy($field, $direction);
                        }
                    }
                }
            } else {
                $query->latest();
            }

            $orders = $query->paginate($rows)->withQueryString();

            return Inertia::render('Admin/Order/Index', [
                'menu' => 'orders',
                'title' => 'Order Management',
                'orders' => $orders,
                'filters' => $request->only(['search', 'rows', 'multiSortMeta']),
            ]);
        } catch (\Throwable $th) {
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Admin/Order/Form', [
            'menu' => 'orders',
            'title' => 'Create Order',
            'users' => User::all(),
            'products' => Product::where('is_active', true)->get(),
            'financialAccounts' => FinancialAccount::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'shipping_address' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,failed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $grandTotal = collect($validated['items'])->sum(function ($item) {
                return $item['selling_price'] * $item['quantity'];
            }) + $validated['shipping_cost'];

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $validated['user_id'],
                'financial_account_id' => $validated['financial_account_id'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_cost' => $validated['shipping_cost'],
                'grand_total' => $grandTotal,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create order: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        $order = Order::with(['user', 'financialAccount', 'orderItems.product'])->findOrFail($id);
        return Inertia::render('Admin/Order/Form', [
            'menu' => 'orders',
            'title' => 'Edit Order ' . $order->order_number,
            'order' => $order,
            'users' => User::all(),
            'products' => Product::where('is_active', true)->get(),
            'financialAccounts' => FinancialAccount::where('is_active', true)->get(),
            'isEdit' => true,
        ]);
    }



    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'shipping_address' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,failed',
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $grandTotal = collect($validated['items'])->sum(function ($item) {
                return $item['selling_price'] * $item['quantity'];
            }) + $validated['shipping_cost'];

            $order->update([
                'user_id' => $validated['user_id'],
                'financial_account_id' => $validated['financial_account_id'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_cost' => $validated['shipping_cost'],
                'grand_total' => $grandTotal,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
            ]);

            // Simple way: delete old items and recreate
            $order->orderItems()->delete();
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update order: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return redirect()->back()->with('success', 'Order deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete order: ' . $th->getMessage());
        }
    }

    public function syncToFinance($id)
    {
        try {
            DB::beginTransaction();
            $order = Order::with('mutation')->findOrFail($id);

            if ($order->mutation) {
                return redirect()->back()->with('warning', 'Order already synced to finance.');
            }

            if ($order->payment_status !== 'paid') {
                return redirect()->back()->with('error', 'Only paid orders can be synced to finance.');
            }

            // Get associated account and category
            $account = $order->financialAccount;
            $category = \App\Models\FinancialCategory::where('type', 'income')->where('name', 'Pemasukan Kantin')->first()
                ?? \App\Models\FinancialCategory::where('type', 'income')->first();

            if (!$account || !$category) {
                return redirect()->back()->with('error', 'Selected Financial Account or Income Category not found.');
            }

            $mutation = \App\Models\FinancialMutation::create([
                'financial_account_id' => $account->id,
                'financial_category_id' => $category->id,
                'flow' => 'in',
                'amount' => $order->grand_total,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => "Penjualan Order: {$order->order_number}",
                'reference_type' => get_class($order),
                'reference_id' => $order->id,
            ]);

            // Update Account Balance
            $account->increment('balance', $mutation->amount);

            DB::commit();
            return redirect()->back()->with('success', 'Order synced to finance successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
                'message' => $th->getMessage(),
            ]);
        }
    }

    private function generateOrderNumber()
    {
        $prefix = 'NF';
        $date = Carbon::now()->format('Ymd'); // Hasil: 20260210

        $countOrder = Order::withTrashed()->count();

        if ($countOrder) {
            $sequence = (int)$countOrder + 1;
        } else {
            $sequence = 1;
        }

        $orderNumber = "{$prefix}-{$date}{$sequence}";

        // Cek Rekursif (Safety Net): Jika tetap bentrok, naikkan sequence
        $exists = Order::where('order_number', $orderNumber)->exists();

        if ($exists) {
            return $this->generateOrderNumber($sequence + 1);
        }

        return $orderNumber;
    }
}
