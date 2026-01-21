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
        Schema::table('dish_categories', function (Blueprint $table) {
            $table->string('name_ru')->nullable()->after('name');
            $table->string('name_ka')->nullable()->after('name_ru');
            $table->text('description_ru')->nullable()->after('description');
            $table->text('description_ka')->nullable()->after('description_ru');
        });

        // Переносим существующие данные в русские поля
        DB::table('dish_categories')->get()->each(function ($category) {
            DB::table('dish_categories')
                ->where('id', $category->id)
                ->update([
                    'name_ru' => $category->name,
                    'description_ru' => $category->description,
                ]);
        });

        // Делаем старые поля nullable
        Schema::table('dish_categories', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dish_categories', function (Blueprint $table) {
            // Переносим данные обратно
            DB::table('dish_categories')->get()->each(function ($category) {
                DB::table('dish_categories')
                    ->where('id', $category->id)
                    ->update([
                        'name' => $category->name_ru ?? $category->name,
                        'description' => $category->description_ru ?? $category->description,
                    ]);
            });

            $table->dropColumn(['name_ru', 'name_ka', 'description_ru', 'description_ka']);
            $table->string('name')->nullable(false)->change();
        });
    }
};
