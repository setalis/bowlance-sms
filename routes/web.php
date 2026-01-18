<?php

use App\Http\Controllers\Admin\ConstructorCategoryController;
use App\Http\Controllers\Admin\ConstructorProductController;
use App\Http\Controllers\Admin\DishCategoryController;
use App\Http\Controllers\Admin\DishController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('frontend.index');
// })->name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard', ['title' => 'Dashboard']);
    })->name('dashboard');

    Route::resource('categories', DishCategoryController::class)->except(['show']);
    Route::resource('dishes', DishController::class)->except(['show']);
    Route::resource('constructor-categories', ConstructorCategoryController::class)->except(['show']);
    Route::resource('constructor-products', ConstructorProductController::class)->except(['show']);
    Route::resource('users', UserController::class);
});

// Backward compatibility redirect
Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect('/admin');
})->name('dashboard');

require __DIR__.'/auth.php';
