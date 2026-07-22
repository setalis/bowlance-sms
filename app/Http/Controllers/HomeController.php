<?php

namespace App\Http\Controllers;

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use App\Models\DishCategory;
use App\Models\Drink;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $title = 'Главная';

        // Загружаем категории блюд с их блюдами
        $dishCategories = DishCategory::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->with(['dishes' => function ($query) {
                $query->orderBy('sort_order')
                    ->orderBy('name')
                    ->with(['addons' => function ($addonQuery) {
                        $addonQuery->where('dish_addons.is_active', true);
                    }]);
            }])
            ->get();

        // Загружаем напитки
        $drinks = Drink::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Загружаем категории конструктора с продуктами
        $constructorCategories = ConstructorCategory::query()
            ->where('type', ConstructorType::Bowl)
            ->orderBy('sort_order')
            ->with('products')
            ->get();

        $breakfastCategories = ConstructorCategory::query()
            ->where('type', ConstructorType::Breakfast)
            ->orderBy('sort_order')
            ->with('products')
            ->get();

        return view('frontend.index', compact('title', 'dishCategories', 'drinks', 'constructorCategories', 'breakfastCategories'));
    }
}
