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
        // Очищаем таблицы перед заполнением
        ConstructorProduct::query()->delete();
        ConstructorCategory::query()->delete();

        // 1. Гарнир, крупа
        $category1 = ConstructorCategory::create([
            'name_ru' => 'Гарнир, крупа',
            'name_ka' => 'გარნირი, ლობიოები',
            'icon_class' => 'icon-[tabler--grain] text-amber-700',
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Гречка',
            'name_ka' => 'ქერი',
            'price' => 4.00,
            'weight_volume' => '100 г',
            'calories' => 92,
            'proteins' => 3.4,
            'fats' => 0.8,
            'carbohydrates' => 18.0,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Рис белый',
            'name_ka' => 'თეთრი ბრინჯი',
            'price' => 4.00,
            'weight_volume' => '100 г',
            'calories' => 112,
            'proteins' => 2.4,
            'fats' => 0.3,
            'carbohydrates' => 25.0,
            'fiber' => 0.0,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Рис черный дикий',
            'name_ka' => 'შავი ველური ბრინჯი',
            'price' => 5.00,
            'weight_volume' => '100 г',
            'calories' => 101,
            'proteins' => 4.0,
            'fats' => 0.3,
            'carbohydrates' => 21.0,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Рис бурый цельнозерновой',
            'name_ka' => 'მთელი მარცვლოვანი ყავისფერი ბრინჯი',
            'price' => 5.00,
            'weight_volume' => '100 г',
            'calories' => 120,
            'proteins' => 2.7,
            'fats' => 1.0,
            'carbohydrates' => 24.0,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Чечевица',
            'name_ka' => 'ოსპი',
            'price' => 4.00,
            'weight_volume' => '100 г',
            'calories' => 116,
            'proteins' => 9.0,
            'fats' => 0.4,
            'carbohydrates' => 20.0,
            'fiber' => 0.0,
            'sort_order' => 5,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Картофель запеченый',
            'name_ka' => 'შემწვარი კარტოფილი',
            'price' => 4.00,
            'weight_volume' => '100 г',
            'calories' => 140,
            'proteins' => 3.2,
            'fats' => 2.2,
            'carbohydrates' => 27.0,
            'fiber' => 0.0,
            'sort_order' => 6,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category1->id,
            'name_ru' => 'Батат',
            'name_ka' => 'ბატატი',
            'price' => 6.00,
            'weight_volume' => '100 г',
            'calories' => 128,
            'proteins' => 1.7,
            'fats' => 1.0,
            'carbohydrates' => 22.0,
            'fiber' => 0.0,
            'sort_order' => 7,
        ]);

        // 2. Мясо, рыба, птица
        $category2 = ConstructorCategory::create([
            'name_ru' => 'Мясо, рыба, птица',
            'name_ka' => 'ხორცი, თევზი, ფრინველი',
            'icon_class' => 'icon-[tabler--meat] text-red-600',
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Тунец татаки',
            'name_ka' => 'ტუნა ტატაკი',
            'price' => 17.00,
            'weight_volume' => '45 г',
            'calories' => 64,
            'proteins' => 14.3,
            'fats' => 0.3,
            'carbohydrates' => 0.1,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Королевская креветка',
            'name_ka' => 'სამეფო კრევეტი',
            'price' => 12.00,
            'weight_volume' => '50 г',
            'calories' => 55,
            'proteins' => 11.5,
            'fats' => 1.5,
            'carbohydrates' => 0.2,
            'fiber' => 0.0,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Норвежский лосось',
            'name_ka' => 'ნორვეგიული ორაგული',
            'price' => 15.00,
            'weight_volume' => '45 г',
            'calories' => 85,
            'proteins' => 10.5,
            'fats' => 4.5,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Куриное филе запеченое',
            'name_ka' => 'შემწვარი ქათმის ფილე',
            'price' => 12.00,
            'weight_volume' => '80 г',
            'calories' => 120,
            'proteins' => 21.5,
            'fats' => 1.2,
            'carbohydrates' => 0.6,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Куриное филе жареное',
            'name_ka' => 'შემწვარი ქათმის ფილე',
            'price' => 12.00,
            'weight_volume' => '80 г',
            'calories' => 146,
            'proteins' => 24.6,
            'fats' => 3.5,
            'carbohydrates' => 0.6,
            'fiber' => 0.0,
            'sort_order' => 5,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Куриное бедро souvlaki',
            'name_ka' => 'ქათმის ბარკალი souvlaki',
            'price' => 10.00,
            'weight_volume' => '90 г',
            'calories' => 231,
            'proteins' => 25.0,
            'fats' => 14.0,
            'carbohydrates' => 0.7,
            'fiber' => 0.0,
            'sort_order' => 6,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category2->id,
            'name_ru' => 'Говядина tenderloin',
            'name_ka' => 'საქონლის ხორცი tenderloin',
            'price' => 19.00,
            'weight_volume' => '50 г',
            'calories' => 100,
            'proteins' => 16.4,
            'fats' => 3.5,
            'carbohydrates' => 0.9,
            'fiber' => 0.0,
            'sort_order' => 7,
        ]);

        // 3. Овощи
        $category3 = ConstructorCategory::create([
            'name_ru' => 'Овощи',
            'name_ka' => 'ბოსტნეული',
            'icon_class' => 'icon-[tabler--carrot] text-orange-500',
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Черри запеченые',
            'name_ka' => 'შემწვარი ჩერი',
            'price' => 4.00,
            'weight_volume' => '50 г',
            'calories' => 8.5,
            'proteins' => 0.45,
            'fats' => 0.1,
            'carbohydrates' => 2.0,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Черри свежие',
            'name_ka' => 'ახალი ჩერი',
            'price' => 4.00,
            'weight_volume' => '100 г',
            'calories' => 18,
            'proteins' => 0.9,
            'fats' => 0.2,
            'carbohydrates' => 3.9,
            'fiber' => 0.0,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Перец запеченый',
            'name_ka' => 'შემწვარი წიწაკა',
            'price' => 5.00,
            'weight_volume' => '40 г',
            'calories' => 11.6,
            'proteins' => 0.6,
            'fats' => 0.1,
            'carbohydrates' => 2.4,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Перец болгарский свежий',
            'name_ka' => 'ახალი ბულგარული წიწაკა',
            'price' => 5.50,
            'weight_volume' => '100 г',
            'calories' => 27,
            'proteins' => 1.0,
            'fats' => 0.2,
            'carbohydrates' => 6.0,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Броколи',
            'name_ka' => 'ბროკოლი',
            'price' => 5.00,
            'weight_volume' => '50 г',
            'calories' => 30,
            'proteins' => 2.7,
            'fats' => 0.4,
            'carbohydrates' => 6.3,
            'fiber' => 0.0,
            'sort_order' => 5,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Цвет. капуста',
            'name_ka' => 'ყვავილოვანი კომბოსტო',
            'price' => 2.50,
            'weight_volume' => '50 г',
            'calories' => 30,
            'proteins' => 2.0,
            'fats' => 0.3,
            'carbohydrates' => 6.2,
            'fiber' => 0.0,
            'sort_order' => 6,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Эдамаме',
            'name_ka' => 'ედამამე',
            'price' => 7.50,
            'weight_volume' => '50 г',
            'calories' => 60,
            'proteins' => 6.1,
            'fats' => 2.7,
            'carbohydrates' => 4.8,
            'fiber' => 0.0,
            'sort_order' => 7,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Стручк. фасоль',
            'name_ka' => 'სტრუქვოვანი ლობიო',
            'price' => 7.50,
            'weight_volume' => '50 г',
            'calories' => 15,
            'proteins' => 1.25,
            'fats' => 0.1,
            'carbohydrates' => 3.15,
            'fiber' => 0.0,
            'sort_order' => 8,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Огурец',
            'name_ka' => 'კიტრი',
            'price' => 5.50,
            'weight_volume' => '100 г',
            'calories' => 15,
            'proteins' => 0.7,
            'fats' => 0.1,
            'carbohydrates' => 3.6,
            'fiber' => 0.0,
            'sort_order' => 9,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Авокадо',
            'name_ka' => 'ავოკადო',
            'price' => 6.00,
            'weight_volume' => '50 г',
            'calories' => 80,
            'proteins' => 1.0,
            'fats' => 7.4,
            'carbohydrates' => 4.3,
            'fiber' => 0.0,
            'sort_order' => 10,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category3->id,
            'name_ru' => 'Морковь',
            'name_ka' => 'სტაფილო',
            'price' => 2.00,
            'weight_volume' => '50 г',
            'calories' => 20,
            'proteins' => 0.45,
            'fats' => 0.1,
            'carbohydrates' => 4.8,
            'fiber' => 0.0,
            'sort_order' => 11,
        ]);

        // 4. Зелень
        $category4 = ConstructorCategory::create([
            'name_ru' => 'Зелень',
            'name_ka' => 'მწვანილი',
            'icon_class' => 'icon-[tabler--leaf] text-green-600',
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Укроп',
            'name_ka' => 'კამა',
            'price' => 1.00,
            'weight_volume' => '5 г',
            'calories' => 2,
            'proteins' => 0.0,
            'fats' => 0.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Петрушка',
            'name_ka' => 'ოხრახუში',
            'price' => 1.00,
            'weight_volume' => '5 г',
            'calories' => 2,
            'proteins' => 0.0,
            'fats' => 0.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Руккола',
            'name_ka' => 'რუკოლა',
            'price' => 3.00,
            'weight_volume' => '10 г',
            'calories' => 2.5,
            'proteins' => 0.0,
            'fats' => 0.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Зеленый лук',
            'name_ka' => 'მწვანე ხახვი',
            'price' => 1.00,
            'weight_volume' => '10 г',
            'calories' => 3,
            'proteins' => 0.0,
            'fats' => 0.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category4->id,
            'name_ru' => 'Микс салата (лола + айсберг)',
            'name_ka' => 'სალათის მიქსი (ლოლა + აისბერგი)',
            'price' => 5.50,
            'weight_volume' => '50 г',
            'calories' => 7,
            'proteins' => 0.0,
            'fats' => 0.0,
            'carbohydrates' => 0.0,
            'fiber' => 0.0,
            'sort_order' => 5,
        ]);

        // 5. Соусы
        $category5 = ConstructorCategory::create([
            'name_ru' => 'Соусы',
            'name_ka' => 'სოუსები',
            'icon_class' => 'icon-[tabler--droplet] text-amber-500',
            'sort_order' => 5,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Арахисовый соус',
            'name_ka' => 'არაქისის სოუსი',
            'price' => 3.00,
            'weight_volume' => '40 г',
            'calories' => 85,
            'proteins' => 4.1,
            'fats' => 7.6,
            'carbohydrates' => 2.7,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Hot honey sauce',
            'name_ka' => 'Hot honey sauce',
            'price' => 4.00,
            'weight_volume' => '40 г',
            'calories' => 86,
            'proteins' => 0.08,
            'fats' => 0.04,
            'carbohydrates' => 21.7,
            'fiber' => 0.0,
            'sort_order' => 2,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Медово - цитрусовый',
            'name_ka' => 'თაფლიანი ციტრუსოვანი',
            'price' => 3.00,
            'weight_volume' => '40 г',
            'calories' => 22,
            'proteins' => 0.37,
            'fats' => 0.09,
            'carbohydrates' => 5.0,
            'fiber' => 0.0,
            'sort_order' => 3,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Дзадзики',
            'name_ka' => 'ძაძიკი',
            'price' => 4.00,
            'weight_volume' => '40 г',
            'calories' => 25,
            'proteins' => 3.4,
            'fats' => 1.1,
            'carbohydrates' => 1.3,
            'fiber' => 0.0,
            'sort_order' => 4,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Творожный',
            'name_ka' => 'ყველის სოუსი',
            'price' => 5.00,
            'weight_volume' => '40 г',
            'calories' => 38,
            'proteins' => 3.4,
            'fats' => 2.0,
            'carbohydrates' => 1.4,
            'fiber' => 0.0,
            'sort_order' => 5,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Манго-мята соус',
            'name_ka' => 'მანგო-პიტნის სოუსი',
            'price' => 5.00,
            'weight_volume' => '40 г',
            'calories' => 24,
            'proteins' => 0.3,
            'fats' => 0.1,
            'carbohydrates' => 5.2,
            'fiber' => 0.0,
            'sort_order' => 6,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Соевый',
            'name_ka' => 'სოიოს სოუსი',
            'price' => 2.00,
            'weight_volume' => '40 г',
            'calories' => 20,
            'proteins' => 1.0,
            'fats' => 0.0,
            'carbohydrates' => 1.5,
            'fiber' => 0.0,
            'sort_order' => 7,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category5->id,
            'name_ru' => 'Йогурт греческий 2% (пустой)',
            'name_ka' => 'ბერძნული იოგურტი 2%',
            'price' => 3.00,
            'weight_volume' => '40 г',
            'calories' => 29,
            'proteins' => 3.6,
            'fats' => 0.8,
            'carbohydrates' => 1.4,
            'fiber' => 0.0,
            'sort_order' => 8,
        ]);

        // 6. Хлебо-булочные изделия
        $category6 = ConstructorCategory::create([
            'name_ru' => 'Хлебо-булочные изделия',
            'name_ka' => 'საფუარებელი ნაწარმი',
            'icon_class' => 'icon-[tabler--bread] text-amber-600',
            'sort_order' => 6,
        ]);

        ConstructorProduct::create([
            'constructor_category_id' => $category6->id,
            'name_ru' => 'Чечевичный хлеб',
            'name_ka' => 'ოსპის პური',
            'price' => 3.00,
            'weight_volume' => '60 г',
            'calories' => 118,
            'proteins' => 9.0,
            'fats' => 3.3,
            'carbohydrates' => 11.2,
            'fiber' => 0.0,
            'sort_order' => 1,
        ]);
    }
}
