<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $Supplier = Supplier::all();
        $User = User::all();

        DB::beginTransaction();

        for ($i = 1; $i <= 10; $i++) {
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'grand_total' => 0,
                'financial_account_id' => \App\Models\FinancialAccount::first()->id,
                'supplier_id' => $Supplier->random()->id,
                'user_id' => $User->random()->id,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'shipping_cost' => rand(0, 100000),
                'notes' => 'Notes ' . $i,
            ]);

            for ($j = 1; $j < rand(1, 5); $j++) {
                $product = Product::all()->random();
                $quantity = rand(1, 5);
                $cost_price = $product->cost_price;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'cost_price' => $cost_price,
                ]);
            }

            $purchaseOrder->update([
                'grand_total' => $purchaseOrder->purchaseOrderItems()
                    ->sum(DB::raw('cost_price * quantity')),
            ]);
        }

        DB::commit();
    }

    private function generatePONumber()
    {
        $prefix = 'PO';
        $date = Carbon::now()->format('Ymd'); // Hasil: 20260210

        $poCount = PurchaseOrder::withTrashed()->count();

        if ($poCount) {
            $sequence = (int)$poCount + 1;
        } else {
            $sequence = 1;
        }

        $orderNumber = "{$prefix}-{$date}{$sequence}";

        // Cek Rekursif (Safety Net): Jika tetap bentrok, naikkan sequence
        $exists = \App\Models\PurchaseOrder::where('po_number', $orderNumber)->exists();

        if ($exists) {
            return $this->generatePONumber($sequence + 1);
        }

        return $orderNumber;
    }
}
