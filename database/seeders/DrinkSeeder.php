<?php

namespace Database\Seeders;

use App\Models\Drink;
use Illuminate\Database\Seeder;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drinks = [
            [
                'name' => 'Боржоми 0.5',
                'name_ru' => 'Боржоми 0.5',
                'name_ka' => 'ბორჯომი 0.5',
                'description' => 'Натуральная минеральная вода из Грузии',
                'description_ru' => 'Натуральная минеральная вода из Грузии',
                'description_ka' => 'ბუნებრივი მინერალური წყალი საქართველოდან',
                'price' => 6.00,
                'volume' => '500 мл',
                'calories' => 0,
                'proteins' => 0,
                'fats' => 0,
                'carbohydrates' => 0,
                'fiber' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Мтис 0.5',
                'name_ru' => 'Мтис 0.5',
                'name_ka' => 'მტის 0.5',
                'description' => 'Природная минеральная вода из горных источников Грузии',
                'description_ru' => 'Природная минеральная вода из горных источников Грузии',
                'description_ka' => 'ბუნებრივი მინერალური წყალი საქართველოს მთის წყაროებიდან',
                'price' => 4.00,
                'volume' => '500 мл',
                'calories' => 0,
                'proteins' => 0,
                'fats' => 0,
                'carbohydrates' => 0,
                'fiber' => 0,
                'sort_order' => 2,
            ],
        ];

        foreach ($drinks as $drinkData) {
            Drink::create($drinkData);
        }
    }
}
