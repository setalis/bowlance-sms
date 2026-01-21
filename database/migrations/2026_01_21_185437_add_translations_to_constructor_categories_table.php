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
        Schema::table('constructor_categories', function (Blueprint $table) {
            $table->string('name_ru')->nullable()->after('name');
            $table->string('name_ka')->nullable()->after('name_ru');
        });

        // Переносим существующие данные в русские поля
        DB::table('constructor_categories')->get()->each(function ($category) {
            DB::table('constructor_categories')
                ->where('id', $category->id)
                ->update([
                    'name_ru' => $category->name,
                ]);
        });

        // Делаем старое поле nullable
        Schema::table('constructor_categories', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('constructor_categories', function (Blueprint $table) {
            // Переносим данные обратно
            DB::table('constructor_categories')->get()->each(function ($category) {
                DB::table('constructor_categories')
                    ->where('id', $category->id)
                    ->update([
                        'name' => $category->name_ru ?? $category->name,
                    ]);
            });

            $table->dropColumn(['name_ru', 'name_ka']);
            $table->string('name')->nullable(false)->change();
        });
    }
};
