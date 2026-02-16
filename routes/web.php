<?php

use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
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

Route::get('/order/{id}', [\App\Http\Controllers\Front\OrderController::class, 'show'])
    ->name('order.show');
Route::get('/order/{id}/print', [\App\Http\Controllers\Front\OrderController::class, 'print'])
    ->name('order.print');

Route::get('/purchase-order/{id}', [\App\Http\Controllers\Front\PurchaseOrderController::class, 'show'])
    ->name('purchase_order.show');


Route::get('/user/permissions', [\App\Http\Controllers\Auth\UserController::class, 'getPermissions'])
    ->name('user.permissions');

Route::get('auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])
    ->name('google.login');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback']);

Route::middleware('auth', 'verified')->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard.index');
        } else {
            return redirect()->route('home');
        }
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('admin.dashboard.index');


        // User routes
        Route::get('/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('admin.users.index');
        Route::post('/user', [\App\Http\Controllers\Admin\UserController::class, 'store'])
            ->name('admin.users.store');
        Route::put('/user/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
            ->name('admin.users.update');
        Route::delete('/user/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
            ->name('admin.users.destroy');

        // Product Category routes
        Route::get('/product-categories', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'index'])
            ->name('admin.product_categories.index');
        Route::post('/product-categories', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'store'])
            ->name('admin.product_categories.store');
        Route::put('/product-categories/{id}', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'update'])
            ->name('admin.product_categories.update');
        Route::delete('/product-categories/{id}', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'destroy'])
            ->name('admin.product_categories.destroy');

        // Product routes
        Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])
            ->name('admin.products.index');
        Route::post('/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])
            ->name('admin.products.store');
        Route::put('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])
            ->name('admin.products.update');
        Route::delete('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])
            ->name('admin.products.destroy');
        Route::post('/products/{id}/duplicate', [\App\Http\Controllers\Admin\ProductController::class, 'duplicate'])
            ->name('admin.products.duplicate');


        // Order routes
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])
            ->name('admin.orders.index');
        Route::get('/orders/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])
            ->name('admin.orders.create');
        Route::post('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'store'])
            ->name('admin.orders.store');
        Route::get('/orders/{id}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])
            ->name('admin.orders.edit');
        Route::put('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])
            ->name('admin.orders.update');
        Route::delete('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])
            ->name('admin.orders.destroy');
        Route::post('/orders/{id}/sync-finance', [\App\Http\Controllers\Admin\OrderController::class, 'syncToFinance'])
            ->name('admin.orders.sync_finance');

        // Financial Account routes
        Route::get('/financial-accounts', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'index'])
            ->name('admin.financial_accounts.index');
        Route::post('/financial-accounts', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'store'])
            ->name('admin.financial_accounts.store');
        Route::put('/financial-accounts/{id}', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'update'])
            ->name('admin.financial_accounts.update');
        Route::delete('/financial-accounts/{id}', [\App\Http\Controllers\Admin\FinancialAccountController::class, 'destroy'])
            ->name('admin.financial_accounts.destroy');

        // Financial Category routes
        Route::get('/financial-categories', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'index'])
            ->name('admin.financial_categories.index');
        Route::post('/financial-categories', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'store'])
            ->name('admin.financial_categories.store');
        Route::put('/financial-categories/{id}', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'update'])
            ->name('admin.financial_categories.update');
        Route::delete('/financial-categories/{id}', [\App\Http\Controllers\Admin\FinancialCategoryController::class, 'destroy'])
            ->name('admin.financial_categories.destroy');

        // Cash Inflow routes
        Route::get('/financial-cash-in', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'index'])
            ->name('admin.financial_cash_in.index');
        Route::post('/financial-cash-in', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'store'])
            ->name('admin.financial_cash_in.store');
        Route::put('/financial-cash-in/{id}', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'update'])
            ->name('admin.financial_cash_in.update');
        Route::delete('/financial-cash-in/{id}', [\App\Http\Controllers\Admin\FinancialCashInController::class, 'destroy'])
            ->name('admin.financial_cash_in.destroy');

        // Cash Outflow routes
        Route::get('/financial-cash-out', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'index'])
            ->name('admin.financial_cash_out.index');
        Route::post('/financial-cash-out', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'store'])
            ->name('admin.financial_cash_out.store');
        Route::put('/financial-cash-out/{id}', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'update'])
            ->name('admin.financial_cash_out.update');
        Route::delete('/financial-cash-out/{id}', [\App\Http\Controllers\Admin\FinancialCashOutController::class, 'destroy'])
            ->name('admin.financial_cash_out.destroy');

        // Financial Transaction (History) routes
        Route::get('/financial_transactions', [\App\Http\Controllers\Admin\FinancialTransactionController::class, 'index'])
            ->name('admin.financial_transactions.index');

        // Supplier routes
        Route::get('/suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'index'])
            ->name('admin.suppliers.index');
        Route::post('/suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'store'])
            ->name('admin.suppliers.store');
        Route::put('/suppliers/{id}', [\App\Http\Controllers\Admin\SupplierController::class, 'update'])
            ->name('admin.suppliers.update');
        Route::delete('/suppliers/{id}', [\App\Http\Controllers\Admin\SupplierController::class, 'destroy'])
            ->name('admin.suppliers.destroy');

        // Purchase Order routes
        Route::get('/purchase-orders', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'index'])
            ->name('admin.purchase_orders.index');
        Route::get('/purchase-orders/create', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'create'])
            ->name('admin.purchase_orders.create');
        Route::post('/purchase-orders', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'store'])
            ->name('admin.purchase_orders.store');
        Route::get('/purchase-orders/{id}/edit', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'edit'])
            ->name('admin.purchase_orders.edit');
        Route::put('/purchase-orders/{id}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'update'])
            ->name('admin.purchase_orders.update');
        Route::delete('/purchase-orders/{id}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'destroy'])
            ->name('admin.purchase_orders.destroy');
        Route::post('/purchase-orders/{id}/sync-finance', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'syncToFinance'])
            ->name('admin.purchase_orders.sync_finance');

        // Role routes
        Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])
            ->name('admin.roles.index');
        Route::get('/roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])
            ->name('admin.roles.create');
        Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])
            ->name('admin.roles.store');
        Route::get('/roles/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])
            ->name('admin.roles.edit');
        Route::put('/roles/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])
            ->name('admin.roles.update');
        Route::delete('/roles/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])
            ->name('admin.roles.destroy');
        Route::get('/roles/{id}/users', [\App\Http\Controllers\Admin\RoleController::class, 'getUsers'])
            ->name('admin.roles.users');
        Route::post('/roles/{id}/toggle-user', [\App\Http\Controllers\Admin\RoleController::class, 'toggleUser'])
            ->name('admin.roles.users.toggle');



        // Settings routes
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])
            ->name('admin.settings.index');
        Route::get('/settings/data', [\App\Http\Controllers\Admin\SettingController::class, 'getSettings'])
            ->name('admin.settings.data');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])
            ->name('admin.settings.update');
        Route::post('/settings/delete-slider', [\App\Http\Controllers\Admin\SettingController::class, 'deleteSliderImage'])
            ->name('admin.settings.delete_slider');


        // Utilities routes
        Route::get('/utilities', [\App\Http\Controllers\Admin\UtilitiesController::class, 'index'])
            ->name('admin.utilities.index');
        Route::post('/utilities/clear-cache', [\App\Http\Controllers\Admin\UtilitiesController::class, 'clearCache'])
            ->name('admin.utilities.clear_cache');
        Route::post('/utilities/clear-log', [\App\Http\Controllers\Admin\UtilitiesController::class, 'clearLog'])
            ->name('admin.utilities.clear_log');
        Route::post('/utilities/toggle-debug', [\App\Http\Controllers\Admin\UtilitiesController::class, 'toggleDebug'])
            ->name('admin.utilities.toggle_debug');
        Route::post('/utilities/clear-session', [\App\Http\Controllers\Admin\UtilitiesController::class, 'clearSession'])
            ->name('admin.utilities.clear_session');
    });
});



require __DIR__ . '/auth.php';
