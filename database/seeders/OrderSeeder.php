<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = \App\Models\Product::get();
        $users = \App\Models\User::get();
        
        for($i = 0; $i < 50; $i++) {
            $product = $products->random();
            $user = $users->random();
            
            // commit
            DB::beginTransaction();
            $order = \App\Models\Order::create([
                'order_number' => $this->generateOrderNumber(),                
                'grand_total' => 0,
                'status' => 'completed',
                'payment_status' => 'paid',                
                'shipping_address' => 'Shipping Address',
                'shipping_cost' => 0,
                'user_id' => $user->id,               
            ]);

            for($j = 0; $j < rand(1, 5); $j++) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                ]);
            }

            $order->update([
                'grand_total' => $order->orderItems->sum('price') + $order->shipping_cost,
            ]);
            DB::commit();   
        }
    }

    private function generateOrderNumber()
    {
        $prefix = 'NF';
        $date = Carbon::now()->format('Ymd'); // Hasil: 20260210

        $countOrder = \App\Models\Order::withTrashed()->count();

        if ($countOrder) {
            $sequence = (int)$countOrder + 1;
        } else {
            $sequence = 1;
        }

        $orderNumber = "{$prefix}-{$date}{$sequence}";

        // Cek Rekursif (Safety Net): Jika tetap bentrok, naikkan sequence
        $exists = \App\Models\Order::where('order_number', $orderNumber)->exists();

        if ($exists) {
            return $this->generateOrderNumber($sequence + 1);
        }

        return $orderNumber;
    }
}
