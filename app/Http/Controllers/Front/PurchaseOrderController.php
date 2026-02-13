<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function show($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::with([
                'supplier',
                'financialAccount',
                'purchaseOrderItems.product.category',
                'user'
            ])->findOrFail($id);

            return Inertia::render('Front/PurchaseOrder/Show', [
                'menu' => 'purchase_orders',
                'title' => 'Purchase Order Detail ' . $purchaseOrder->po_number,
                'purchaseOrder' => $purchaseOrder,
            ]);
        } catch (\Throwable $th) {
            // set error 500
            return Inertia::render('Errors/NotFound', [
                'status' => 500,
            ]);
        }
    }
}
