<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/produk', [\App\Http\Controllers\Front\ProdukController::class, 'index'])
    ->name('front.produk.index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard.index');

    Route::get('/admin/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])
        ->name('admin.users.index');
    Route::post('/admin/user', [\App\Http\Controllers\Admin\UserController::class, 'store'])
        ->name('admin.users.store');
    Route::put('/admin/user/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])
        ->name('admin.users.update');
    Route::delete('/admin/user/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
        ->name('admin.users.destroy');
});

require __DIR__ . '/auth.php';
