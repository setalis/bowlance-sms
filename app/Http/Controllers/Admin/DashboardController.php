<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Drink;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users_count' => User::query()->count(),
            'orders_count' => Order::query()->count(),
            'orders_new_count' => Order::query()->where('status', OrderStatus::New)->count(),
            'orders_in_progress_count' => Order::query()->where('status', OrderStatus::InProgress)->count(),
            'orders_completed_count' => Order::query()->where('status', OrderStatus::Completed)->count(),
            'revenue' => Order::query()->where('status', OrderStatus::Completed)->sum('total'),
            'dishes_count' => Dish::query()->count(),
            'dish_categories_count' => DishCategory::query()->count(),
            'drinks_count' => Drink::query()->count(),
            'constructor_categories_count' => ConstructorCategory::query()->count(),
            'constructor_products_count' => ConstructorProduct::query()->count(),
        ];

        $recentOrders = Order::query()
            ->with('items')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
