<?php

namespace Database\Seeders;

use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use Illuminate\Database\Seeder;

class ConstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Гарнир, крупа
        $category1 = ConstructorCategory::create([
            'name_ru' => 'Гарнир, крупа',
            'name_ka' => 'გარნირი, ლობიოები',
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Картофель запеченный',
            'name_ka' => 'შემწვარი კარტოფილი',
            'price' => 50.00,
            'weight_volume' => '150 г',
            'calories' => 110,
            'proteins' => 2.5,
            'fats' => 0.2,
            'carbohydrates' => 25.0,
            'fiber' => 2.2,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Гречка',
            'name_ka' => 'ქერი',
            'price' => 45.00,
            'weight_volume' => '150 г',
            'calories' => 155,
            'proteins' => 5.7,
            'fats' => 1.5,
            'carbohydrates' => 30.0,
            'fiber' => 4.5,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Рис бурый',
            'name_ka' => 'ყავისფერი ბრინჯი',
            'price' => 40.00,
            'weight_volume' => '150 г',
            'calories' => 165,
            'proteins' => 3.5,
            'fats' => 1.2,
            'carbohydrates' => 34.0,
            'fiber' => 1.8,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Киноа',
            'name_ka' => 'კინოა',
            'price' => 55.00,
            'weight_volume' => '150 г',
            'calories' => 180,
            'proteins' => 6.0,
            'fats' => 2.8,
            'carbohydrates' => 30.0,
            'fiber' => 3.5,
            'sort_order' => 4,
        ]);

        // 2. Мясо, рыба, птица
        $category2 = ConstructorCategory::create([
            'name_ru' => 'Мясо, рыба, птица',
            'name_ka' => 'ხორცი, თევზი, ფრინველი',
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Говядина (сирлоин)',
            'name_ka' => 'საქონლის ხორცი (სირლოინი)',
            'price' => 200.00,
            'weight_volume' => '120 г',
            'calories' => 250,
            'proteins' => 26.0,
            'fats' => 15.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Куриное филе',
            'name_ka' => 'ქათმის ფილე',
            'price' => 150.00,
            'weight_volume' => '120 г',
            'calories' => 165,
            'proteins' => 31.0,
            'fats' => 3.6,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Лосось',
            'name_ka' => 'ორაგული',
            'price' => 250.00,
            'weight_volume' => '120 г',
            'calories' => 230,
            'proteins' => 25.0,
            'fats' => 13.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Индейка',
            'name_ka' => 'ინდაური',
            'price' => 160.00,
            'weight_volume' => '120 г',
            'calories' => 135,
            'proteins' => 30.0,
            'fats' => 1.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);

        // 3. Овощи
        $category3 = ConstructorCategory::create([
            'name_ru' => 'Овощи',
            'name_ka' => 'ბოსტნეული',
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Огурец',
            'name_ka' => 'კიტრი',
            'price' => 30.00,
            'weight_volume' => '80 г',
            'calories' => 12,
            'proteins' => 0.6,
            'fats' => 0.1,
            'carbohydrates' => 2.2,
            'fiber' => 0.5,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Помидор',
            'name_ka' => 'პომიდორი',
            'price' => 35.00,
            'weight_volume' => '80 г',
            'calories' => 14,
            'proteins' => 0.7,
            'fats' => 0.2,
            'carbohydrates' => 2.6,
            'fiber' => 0.9,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Перец болгарский',
            'name_ka' => 'ბულგარული წიწაკა',
            'price' => 40.00,
            'weight_volume' => '80 г',
            'calories' => 20,
            'proteins' => 0.8,
            'fats' => 0.2,
            'carbohydrates' => 4.6,
            'fiber' => 1.7,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Брокколи',
            'name_ka' => 'ბროკოლი',
            'price' => 45.00,
            'weight_volume' => '100 г',
            'calories' => 34,
            'proteins' => 2.8,
            'fats' => 0.4,
            'carbohydrates' => 7.0,
            'fiber' => 2.6,
            'sort_order' => 4,
        ]);

        // 4. Зелень
        $category4 = ConstructorCategory::create([
            'name_ru' => 'Зелень',
            'name_ka' => 'მწვანილი',
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Укроп',
            'name_ka' => 'კამა',
            'price' => 20.00,
            'weight_volume' => '10 г',
            'calories' => 4,
            'proteins' => 0.3,
            'fats' => 0.1,
            'carbohydrates' => 0.7,
            'fiber' => 0.2,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Петрушка',
            'name_ka' => 'ოხრახუში',
            'price' => 20.00,
            'weight_volume' => '10 г',
            'calories' => 4,
            'proteins' => 0.3,
            'fats' => 0.1,
            'carbohydrates' => 0.6,
            'fiber' => 0.3,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Базилик',
            'name_ka' => 'რეჰანი',
            'price' => 25.00,
            'weight_volume' => '10 г',
            'calories' => 2,
            'proteins' => 0.3,
            'fats' => 0.1,
            'carbohydrates' => 0.3,
            'fiber' => 0.2,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Кинза',
            'name_ka' => 'ქინძი',
            'price' => 22.00,
            'weight_volume' => '10 г',
            'calories' => 2,
            'proteins' => 0.2,
            'fats' => 0.1,
            'carbohydrates' => 0.4,
            'fiber' => 0.3,
            'sort_order' => 4,
        ]);

        // 5. Соусы
        $category5 = ConstructorCategory::create([
            'name_ru' => 'Соусы',
            'name_ka' => 'სოუსები',
            'sort_order' => 5,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Ткемали',
            'name_ka' => 'ტყემალი',
            'price' => 50.00,
            'weight_volume' => '30 мл',
            'calories' => 35,
            'proteins' => 0.5,
            'fats' => 0.0,
            'carbohydrates' => 8.0,
            'fiber' => 0.5,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Тартар',
            'name_ka' => 'ტარტარი',
            'price' => 45.00,
            'weight_volume' => '30 мл',
            'calories' => 120,
            'proteins' => 0.5,
            'fats' => 12.0,
            'carbohydrates' => 2.0,
            'fiber' => 0.2,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Сырный соус',
            'name_ka' => 'ყველის სოუსი',
            'price' => 55.00,
            'weight_volume' => '30 мл',
            'calories' => 100,
            'proteins' => 3.0,
            'fats' => 8.0,
            'carbohydrates' => 4.0,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Оливковое масло',
            'name_ka' => 'ზეითუნის ზეთი',
            'price' => 40.00,
            'weight_volume' => '10 мл',
            'calories' => 88,
            'proteins' => 0.0,
            'fats' => 10.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);
    }
}
