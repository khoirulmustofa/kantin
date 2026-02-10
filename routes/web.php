<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\Front\HomeController::class, 'index'])
    ->name('home');
Route::get('/product', [\App\Http\Controllers\Front\ProductController::class, 'index'])
    ->name('product.index');
Route::get('/product/{slug}', [\App\Http\Controllers\Front\ProductController::class, 'show'])
    ->name('product.show');

Route::get('/cart', [\App\Http\Controllers\Front\CartController::class, 'index'])
    ->name('cart.index');

Route::get('/checkout', [\App\Http\Controllers\Front\CheckoutController::class, 'index'])
    ->name('checkout.index');
Route::post('/checkout/store', [\App\Http\Controllers\Front\CheckoutController::class, 'store'])
    ->name('checkout.store');

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
    Route::get('/admin/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])
        ->name('admin.orders.show');
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

    // Supplier routes
    Route::get('/admin/suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'index'])
        ->name('admin.suppliers.index');
    Route::post('/admin/suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'store'])
        ->name('admin.suppliers.store');
    Route::put('/admin/suppliers/{id}', [\App\Http\Controllers\Admin\SupplierController::class, 'update'])
        ->name('admin.suppliers.update');
    Route::delete('/admin/suppliers/{id}', [\App\Http\Controllers\Admin\SupplierController::class, 'destroy'])
        ->name('admin.suppliers.destroy');

    // Purchase Order routes
    Route::get('/admin/purchase-orders', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'index'])
        ->name('admin.purchase-orders.index');
    Route::get('/admin/purchase-orders/create', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'create'])
        ->name('admin.purchase-orders.create');
    Route::post('/admin/purchase-orders', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'store'])
        ->name('admin.purchase-orders.store');
    Route::get('/admin/purchase-orders/{id}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'show'])
        ->name('admin.purchase-orders.show');
    Route::get('/admin/purchase-orders/{id}/edit', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'edit'])
        ->name('admin.purchase-orders.edit');
    Route::put('/admin/purchase-orders/{id}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'update'])
        ->name('admin.purchase-orders.update');
    Route::delete('/admin/purchase-orders/{id}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'destroy'])
        ->name('admin.purchase-orders.destroy');
    Route::post('/admin/purchase-orders/{id}/sync-finance', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'syncToFinance'])
        ->name('admin.purchase-orders.sync_finance');
});

require __DIR__ . '/auth.php';
