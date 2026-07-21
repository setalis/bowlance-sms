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
        Schema::table('constructor_products', function (Blueprint $table) {
            $table->unsignedInteger('poster_bowl_modification_id')->nullable()->after('sort_order');
            $table->unsignedInteger('poster_breakfast_modification_id')->nullable()->after('poster_bowl_modification_id');
        });

        $products = DB::table('constructor_products')
            ->whereNotNull('poster_modification_id')
            ->get(['id', 'poster_modification_id']);

        foreach ($products as $product) {
            DB::table('constructor_products')
                ->where('id', $product->id)
                ->update(['poster_bowl_modification_id' => $product->poster_modification_id]);
        }

        Schema::table('constructor_products', function (Blueprint $table) {
            $table->dropColumn('poster_modification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constructor_products', function (Blueprint $table) {
            $table->unsignedInteger('poster_modification_id')->nullable()->after('sort_order');
        });

        $products = DB::table('constructor_products')
            ->whereNotNull('poster_bowl_modification_id')
            ->get(['id', 'poster_bowl_modification_id']);

        foreach ($products as $product) {
            DB::table('constructor_products')
                ->where('id', $product->id)
                ->update(['poster_modification_id' => $product->poster_bowl_modification_id]);
        }

        Schema::table('constructor_products', function (Blueprint $table) {
            $table->dropColumn(['poster_bowl_modification_id', 'poster_breakfast_modification_id']);
        });
    }
};
