<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/produk', [\App\Http\Controllers\ProductController::class, 'index'])
    ->name('front.produk.index');
Route::get('/produk/{slug}', [\App\Http\Controllers\ProductController::class, 'show'])
    ->name('front.produk.show');

Route::get('/cart', function () {
    return Inertia::render('Cart/Index', [
        'menu' => 'cart',
        'title' => 'Shopping Cart',
    ]);
})->name('front.cart.index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard.index');


    // User routes
    Route::get('/admin/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])
        ->name('admin.users.index');
    Route::post('/admin/user', [\App\Http\Controllers\Admin\UserController::class, 'store'])
        ->name('admin.users.store');
    Route::put('/admin/user/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
        ->name('admin.users.update');
    Route::delete('/admin/user/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
        ->name('admin.users.destroy');

    // Product Category routes
    Route::get('/admin/product-categories', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'index'])
        ->name('admin.product-categories.index');
    Route::post('/admin/product-categories', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'store'])
        ->name('admin.product-categories.store');
    Route::put('/admin/product-categories/{id}', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'update'])
        ->name('admin.product-categories.update');
    Route::delete('/admin/product-categories/{id}', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'destroy'])
        ->name('admin.product-categories.destroy');

    // Product routes
    Route::get('/admin/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])
        ->name('admin.products.index');
    Route::post('/admin/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])
        ->name('admin.products.store');
    Route::put('/admin/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])
        ->name('admin.products.update');
    Route::delete('/admin/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])
        ->name('admin.products.destroy');

    // Order routes
    Route::get('/admin/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])
        ->name('admin.orders.index');
    Route::get('/admin/orders/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])
        ->name('admin.orders.create');
    Route::post('/admin/orders', [\App\Http\Controllers\Admin\OrderController::class, 'store'])
        ->name('admin.orders.store');
    Route::get('/admin/orders/{id}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])
        ->name('admin.orders.edit');
    Route::put('/admin/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])
        ->name('admin.orders.update');
    Route::delete('/admin/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])
        ->name('admin.orders.destroy');
    Route::post('/admin/orders/{id}/sync-finance', [\App\Http\Controllers\Admin\OrderController::class, 'syncToFinance'])
        ->name('admin.orders.sync_finance');

    // Financial Account routes
    Route::get('/admin/financial-accounts', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'index'])
        ->name('admin.financial_accounts.index');
    Route::post('/admin/financial-accounts', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'store'])
        ->name('admin.financial_accounts.store');
    Route::put('/admin/financial-accounts/{id}', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'update'])
        ->name('admin.financial_accounts.update');
    Route::delete('/admin/financial-accounts/{id}', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'destroy'])
        ->name('admin.financial_accounts.destroy');

    // Financial Category routes
    Route::get('/admin/financial-categories', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'index'])
        ->name('admin.financial_categories.index');
    Route::post('/admin/financial-categories', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'store'])
        ->name('admin.financial_categories.store');
    Route::put('/admin/financial-categories/{id}', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'update'])
        ->name('admin.financial_categories.update');
    Route::delete('/admin/financial-categories/{id}', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'destroy'])
        ->name('admin.financial_categories.destroy');

    // Cash Inflow routes
    Route::get('/admin/financial_cash_in', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'index'])
        ->name('admin.financial_cash_in.index');
    Route::post('/admin/financial_cash_in', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'store'])
        ->name('admin.financial_cash_in.store');
    Route::put('/admin/financial_cash_in/{id}', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'update'])
        ->name('admin.financial_cash_in.update');
    Route::delete('/admin/financial_cash_in/{id}', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'destroy'])
        ->name('admin.financial_cash_in.destroy');

    // Cash Outflow routes
    Route::get('/admin/financial_cash_out', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'index'])
        ->name('admin.financial_cash_out.index');
    Route::post('/admin/financial_cash_out', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'store'])
        ->name('admin.financial_cash_out.store');
    Route::put('/admin/financial_cash_out/{id}', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'update'])
        ->name('admin.financial_cash_out.update');
    Route::delete('/admin/financial_cash_out/{id}', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'destroy'])
        ->name('admin.financial_cash_out.destroy');

    // Financial Transaction (History) routes
    Route::get('/admin/financial_transactions', [\App\Http\Controllers\Admin\FinancialTransactionController::class, 'index'])
        ->name('admin.financial_transactions.index');
});

require __DIR__ . '/auth.php';
