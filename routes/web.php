<?php

use App\Http\Controllers\Admin\ConstructorCategoryController;
use App\Http\Controllers\Admin\ConstructorProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DishCategoryController;
use App\Http\Controllers\Admin\DishController;
use App\Http\Controllers\Admin\DrinkController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Cabinet\AddressController as CabinetAddressController;
use App\Http\Controllers\Cabinet\Auth\LoginController as CabinetLoginController;
use App\Http\Controllers\Cabinet\DashboardController;
use App\Http\Controllers\Cabinet\OrderController as CabinetOrderController;
use App\Http\Controllers\Cabinet\ProfileController as CabinetProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('frontend.index');
// })->name('home');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Переключение языка
Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

// Верификация телефона
Route::post('/phone/verify/send', [\App\Http\Controllers\PhoneVerificationController::class, 'send'])->name('phone.verify.send');
Route::post('/phone/verify/check', [\App\Http\Controllers\PhoneVerificationController::class, 'verify'])->name('phone.verify.check');
Route::get('/phone/verify/status', [\App\Http\Controllers\PhoneVerificationController::class, 'status'])->name('phone.verify.status');
Route::post('/phone/verify/telegram/callback', [\App\Http\Controllers\PhoneVerificationController::class, 'telegramCallback'])
    ->middleware(\App\Http\Middleware\LogTelegramCallbackRequest::class)
    ->name('phone.verify.telegram.callback');

// Webhook для бота: сюда Telegram присылает обновления (сообщения пользователя). Обрабатываем /start TOKEN и верифицируем номер.
Route::post('/phone/verify/telegram/webhook', \App\Http\Controllers\TelegramWebhookController::class)->name('phone.verify.telegram.webhook');
// Проверка доступности: откройте в браузере — в storage/logs/laravel.log появится запись
Route::get('/phone/verify/telegram/ping', function () {
    \Illuminate\Support\Facades\Log::info('phone.verify.telegram.ping: callback URL reachable', [
        'url' => request()->fullUrl(),
        'time' => now()->toIso8601String(),
    ]);
    $webhookUrl = route('phone.verify.telegram.webhook');

    return response()->json([
        'reachable' => true,
        'webhook_url' => $webhookUrl,
        'message' => 'Вебхук настроен. Выполните: php artisan telegram:set-webhook',
    ]);
})->name('phone.verify.telegram.ping');
Route::post('/phone/verify/cancel', [\App\Http\Controllers\PhoneVerificationController::class, 'cancel'])->name('phone.verify.cancel');

// Публичные маршруты для заказов
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// API для управления адресами (требует авторизации)
Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('/addresses', [UserAddressController::class, 'index'])->name('user.addresses.index');
    Route::post('/addresses', [UserAddressController::class, 'store'])->name('user.addresses.store');
    Route::put('/addresses/{address}', [UserAddressController::class, 'update'])->name('user.addresses.update');
    Route::delete('/addresses/{address}', [UserAddressController::class, 'destroy'])->name('user.addresses.destroy');
    Route::post('/addresses/{address}/set-default', [UserAddressController::class, 'setDefault'])->name('user.addresses.setDefault');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', DishCategoryController::class)->except(['show']);
    Route::resource('dishes', DishController::class)->except(['show']);
    Route::resource('drinks', DrinkController::class)->except(['show']);
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

// Личный кабинет — вход по телефону (отдельно от админки)
Route::prefix('cabinet')->name('cabinet.')->group(function () {
    Route::get('login', [CabinetLoginController::class, 'create'])->name('login');
    Route::post('login', [CabinetLoginController::class, 'store']);

    Route::middleware(['auth.cabinet', 'cabinet'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [CabinetProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [CabinetProfileController::class, 'update'])->name('profile.update');
        Route::get('orders', [CabinetOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [CabinetOrderController::class, 'show'])->name('orders.show');

        // Управление адресами
        Route::resource('addresses', CabinetAddressController::class)->except(['show']);
        Route::post('addresses/{address}/set-default', [CabinetAddressController::class, 'setDefault'])->name('addresses.setDefault');

        Route::post('logout', [CabinetLoginController::class, 'destroy'])->name('logout');
    });
});

Route::get('/test-vonage', function () {
    $service = app(\App\Services\VonageVerifyService::class);
    $result = $service->sendVerificationCode('+380507082864'); // Ваш реальный номер
    dd($result);
});

require __DIR__.'/auth.php';
