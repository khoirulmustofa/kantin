<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        try {
            return Inertia::render('Front/Checkout/Index', [
                'menu' => 'checkout',
                'title' => 'Checkout',
                'financialAccounts' => FinancialAccount::where('is_active', true)->get(),
             ])->rootView('front');
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
            'username' => 'required|string', // WhatsApp as username
            'name' => 'required|string',
            'financial_account_id' => 'required|uuid|exists:financial_accounts,id',
            'shipping_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|uuid|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        try {
            DB::beginTransaction();

            // 1. Handle User
            $user = User::where('username', $validated['username'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                    'email' => $validated['username'] . '@wa.com', // Dummy email if needed
                    'password' => Hash::make($validated['username']), // Default password
                    'role' => 'user',
                ]);
            }

            // 2. Calculate Totals
            $grandTotal = 0;
            $orderItems = [];
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $subtotal = $product->selling_price * $item['quantity'];
                $grandTotal += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                ];
            }

            // 3. Create Order
            $orderNumber = $this->generateOrderNumber();
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'financial_account_id' => $validated['financial_account_id'],
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_address' => $validated['shipping_address'],
                'shipping_cost' => 0, // Simplified: free shipping or handled elsewhere
            ]);

            // 4. Create Order Items
            foreach ($orderItems as $itemData) {
                $order->orderItems()->create($itemData);
            }

            DB::commit();

            return redirect()->route('home')->with('success', 'Order #' . $orderNumber . ' placed successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    private function generateOrderNumber($sequenceNum = null)
    {
        $prefix = 'NF';
        $date = Carbon::now()->format('Ymd');

        if ($sequenceNum) {
            $sequence = $sequenceNum;
        } else {
            $countOrder = Order::withTrashed()->count();
            $sequence = (int)$countOrder + 1;
        }

        $orderNumber = "{$prefix}-{$date}{$sequence}";

        $exists = Order::where('order_number', $orderNumber)->exists();
        if ($exists) {
            return $this->generateOrderNumber($sequence + 1);
        }

        return $orderNumber;
    }
}
