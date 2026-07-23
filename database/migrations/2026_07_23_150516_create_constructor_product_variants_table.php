<?php

use App\Enums\ConstructorType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constructor_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('constructor_product_id')->constrained('constructor_products')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('weight_volume')->nullable();
            $table->integer('calories')->nullable();
            $table->decimal('proteins', 5, 2)->nullable();
            $table->decimal('fats', 5, 2)->nullable();
            $table->decimal('carbohydrates', 5, 2)->nullable();
            $table->decimal('fiber', 5, 2)->nullable();
            $table->unsignedInteger('poster_modification_id')->nullable();
            $table->timestamps();

            $table->unique(['constructor_product_id', 'type'], 'constructor_product_variant_type_unique');
        });

        $products = DB::table('constructor_products')->orderBy('id')->get();

        foreach ($products as $product) {
            $types = DB::table('constructor_category_product')
                ->join('constructor_categories', 'constructor_categories.id', '=', 'constructor_category_product.constructor_category_id')
                ->where('constructor_category_product.constructor_product_id', $product->id)
                ->distinct()
                ->pluck('constructor_categories.type')
                ->filter()
                ->values()
                ->all();

            if ($types === []) {
                $types = [ConstructorType::Bowl->value];
            }

            foreach ($types as $type) {
                $posterId = $type === ConstructorType::Breakfast->value
                    ? ($product->poster_breakfast_modification_id ?? null)
                    : ($product->poster_bowl_modification_id ?? null);

                DB::table('constructor_product_variants')->insert([
                    'constructor_product_id' => $product->id,
                    'type' => $type,
                    'price' => $product->price,
                    'weight_volume' => $product->weight_volume,
                    'calories' => $product->calories,
                    'proteins' => $product->proteins,
                    'fats' => $product->fats,
                    'carbohydrates' => $product->carbohydrates,
                    'fiber' => $product->fiber,
                    'poster_modification_id' => $posterId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('constructor_products', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'weight_volume',
                'calories',
                'proteins',
                'fats',
                'carbohydrates',
                'fiber',
                'poster_bowl_modification_id',
                'poster_breakfast_modification_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('constructor_products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('name_ka');
            $table->string('weight_volume')->nullable()->after('description_ka');
            $table->integer('calories')->nullable()->after('weight_volume');
            $table->decimal('proteins', 5, 2)->nullable()->after('calories');
            $table->decimal('fats', 5, 2)->nullable()->after('proteins');
            $table->decimal('carbohydrates', 5, 2)->nullable()->after('fats');
            $table->decimal('fiber', 5, 2)->nullable()->after('carbohydrates');
            $table->unsignedInteger('poster_bowl_modification_id')->nullable()->after('sort_order');
            $table->unsignedInteger('poster_breakfast_modification_id')->nullable()->after('poster_bowl_modification_id');
        });

        $variants = DB::table('constructor_product_variants')->orderBy('id')->get();
        $restored = [];

        foreach ($variants as $variant) {
            if (in_array($variant->constructor_product_id, $restored, true)) {
                if ($variant->type === ConstructorType::Breakfast->value) {
                    DB::table('constructor_products')
                        ->where('id', $variant->constructor_product_id)
                        ->update(['poster_breakfast_modification_id' => $variant->poster_modification_id]);
                }

                continue;
            }

            DB::table('constructor_products')
                ->where('id', $variant->constructor_product_id)
                ->update([
                    'price' => $variant->price,
                    'weight_volume' => $variant->weight_volume,
                    'calories' => $variant->calories,
                    'proteins' => $variant->proteins,
                    'fats' => $variant->fats,
                    'carbohydrates' => $variant->carbohydrates,
                    'fiber' => $variant->fiber,
                    'poster_bowl_modification_id' => $variant->type === ConstructorType::Bowl->value
                        ? $variant->poster_modification_id
                        : null,
                    'poster_breakfast_modification_id' => $variant->type === ConstructorType::Breakfast->value
                        ? $variant->poster_modification_id
                        : null,
                ]);

            $restored[] = $variant->constructor_product_id;
        }

        Schema::dropIfExists('constructor_product_variants');
    }
};
