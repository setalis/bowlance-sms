<?php

namespace Database\Seeders;

use App\Models\DishCategory;
use Illuminate\Database\Seeder;

class DishCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_ru' => 'Основные блюда',
                'name_ka' => 'ძირითადი კერძები',
                'slug' => 'osnovnye-blyuda',
                'is_active' => true,
                'sort' => 1,
            ],
            [
                'name_ru' => 'Салаты',
                'name_ka' => 'სალათები',
                'slug' => 'salaty',
                'is_active' => true,
                'sort' => 2,
            ],
            [
                'name_ru' => 'Закуски',
                'name_ka' => 'წახემსები',
                'slug' => 'zakuski',
                'is_active' => true,
                'sort' => 3,
            ],
            [
                'name_ru' => 'Супы',
                'name_ka' => 'სუპები',
                'slug' => 'supy',
                'is_active' => true,
                'sort' => 4,
            ],
            [
                'name_ru' => 'Десерты',
                'name_ka' => 'დესერტები',
                'slug' => 'deserty',
                'is_active' => true,
                'sort' => 5,
            ],
        ];

        foreach ($categories as $category) {
            DishCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
