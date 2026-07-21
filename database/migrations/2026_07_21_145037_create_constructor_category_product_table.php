<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('constructor_category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('constructor_category_id')->constrained('constructor_categories')->cascadeOnDelete();
            $table->foreignId('constructor_product_id')->constrained('constructor_products')->cascadeOnDelete();
            $table->unique(
                ['constructor_category_id', 'constructor_product_id'],
                'constructor_category_product_unique'
            );
        });

        $products = DB::table('constructor_products')
            ->whereNotNull('constructor_category_id')
            ->get(['id', 'constructor_category_id']);

        foreach ($products as $product) {
            DB::table('constructor_category_product')->insert([
                'constructor_category_id' => $product->constructor_category_id,
                'constructor_product_id' => $product->id,
            ]);
        }

        Schema::table('constructor_products', function (Blueprint $table) {
            $table->dropForeign(['constructor_category_id']);
            $table->dropColumn('constructor_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constructor_products', function (Blueprint $table) {
            $table->foreignId('constructor_category_id')
                ->nullable()
                ->after('id')
                ->constrained('constructor_categories')
                ->cascadeOnDelete();
        });

        $pivots = DB::table('constructor_category_product')
            ->orderBy('id')
            ->get(['constructor_category_id', 'constructor_product_id']);

        $restoredProductIds = [];

        foreach ($pivots as $pivot) {
            if (in_array($pivot->constructor_product_id, $restoredProductIds, true)) {
                continue;
            }

            DB::table('constructor_products')
                ->where('id', $pivot->constructor_product_id)
                ->update(['constructor_category_id' => $pivot->constructor_category_id]);

            $restoredProductIds[] = $pivot->constructor_product_id;
        }

        Schema::dropIfExists('constructor_category_product');
    }
};
