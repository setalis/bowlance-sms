<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->string('sauce_name')->nullable()->after('fiber');
            $table->string('sauce_name_ru')->nullable()->after('sauce_name');
            $table->string('sauce_name_ka')->nullable()->after('sauce_name_ru');
            $table->string('sauce_weight_volume')->nullable()->after('sauce_name_ka');
            $table->integer('sauce_calories')->nullable()->after('sauce_weight_volume');
            $table->decimal('sauce_proteins', 5, 2)->nullable()->after('sauce_calories');
            $table->decimal('sauce_fats', 5, 2)->nullable()->after('sauce_proteins');
            $table->decimal('sauce_carbohydrates', 5, 2)->nullable()->after('sauce_fats');
            $table->decimal('sauce_fiber', 5, 2)->nullable()->after('sauce_carbohydrates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dishes', function (Blueprint $table) {
            $table->dropColumn([
                'sauce_name',
                'sauce_name_ru',
                'sauce_name_ka',
                'sauce_weight_volume',
                'sauce_calories',
                'sauce_proteins',
                'sauce_fats',
                'sauce_carbohydrates',
                'sauce_fiber',
            ]);
        });
    }
};
