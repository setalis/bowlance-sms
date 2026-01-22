<?php

use App\Http\Controllers\Admin\ConstructorCategoryController;
use App\Http\Controllers\Admin\ConstructorProductController;
use App\Http\Controllers\Admin\DishCategoryController;
use App\Http\Controllers\Admin\DishController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('frontend.index');
// })->name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Переключение языка
Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

// Публичные маршруты для заказов
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard', ['title' => 'Dashboard']);
    })->name('dashboard');

    Route::resource('categories', DishCategoryController::class)->except(['show']);
    Route::resource('dishes', DishController::class)->except(['show']);
    Route::resource('constructor-categories', ConstructorCategoryController::class)->except(['show']);
    Route::resource('constructor-products', ConstructorProductController::class)->except(['show']);
    Route::resource('users', UserController::class);

    // Заказы
    Route::resource('orders', AdminOrderController::class)->except(['show']);
    Route::get('orders/{order}/details', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// Backward compatibility redirect
Route::middleware(['auth', 'admin'])->get('/dashboard', function () {
    return redirect('/admin');
})->name('dashboard');

require __DIR__.'/auth.php';
