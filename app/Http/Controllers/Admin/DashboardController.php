<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalIn = \App\Models\FinancialMutation::where('flow', 'in')->sum('amount');
            $totalOut = \App\Models\FinancialMutation::where('flow', 'out')->sum('amount');
            $totalBalance = $totalIn - $totalOut;

            // Chart Data: Last 30 Days
            $days = 30;
            $startDate = now()->subDays($days - 1)->startOfDay();

            $mutations = \App\Models\FinancialMutation::selectRaw('DATE(transaction_date) as date, flow, SUM(amount) as total')
                ->where('transaction_date', '>=', $startDate)
                ->groupBy('date', 'flow')
                ->orderBy('date')
                ->get();

            $labels = [];
            $inData = [];
            $outData = [];

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $labels[] = $startDate->copy()->addDays($i)->format('d M');

                $inData[] = $mutations->where('date', $date)->where('flow', 'in')->first()->total ?? 0;
                $outData[] = $mutations->where('date', $date)->where('flow', 'out')->first()->total ?? 0;
            }

            return Inertia::render('Admin/Dashboard/Index', [
                'menu' => 'dashboard',
                'title' => 'Dashboard',
                'stats' => [
                    'total_in' => (float) $totalIn,
                    'total_out' => (float) $totalOut,
                    'total_balance' => (float) $totalBalance,
                    'total_orders' => \App\Models\Order::count(),
                ],
                'chartData' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Income',
                            'backgroundColor' => '#10b981',
                            'borderColor' => '#10b981',
                            'data' => $inData,
                            'borderRadius' => 6,
                        ],
                        [
                            'label' => 'Expense',
                            'backgroundColor' => '#f43f5e',
                            'borderColor' => '#f43f5e',
                            'data' => $outData,
                            'borderRadius' => 6,
                        ]
                    ]
                ],
                'recent_products' => \App\Models\Product::with('category')->latest()->take(5)->get(),
            ]);
        } catch (\Throwable $th) {
            // ke error 500
            return Inertia::render('Errors/Error500', [
                'status' => false,
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'code' => $th->getCode(),
            ]);
        }
    }
}
