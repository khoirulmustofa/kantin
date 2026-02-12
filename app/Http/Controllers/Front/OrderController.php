<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['user', 'financialAccount', 'orderItems.product.category'])->findOrFail($id);
        return Inertia::render('Front/Order/Show', [
            'menu' => 'orders',
            'title' => 'Order Detail',
            'order' => $order,
        ]);
    }
}
