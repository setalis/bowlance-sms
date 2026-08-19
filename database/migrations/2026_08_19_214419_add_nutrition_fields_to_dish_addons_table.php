<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dish_addons', function (Blueprint $table) {
            $table->integer('calories')->nullable()->after('price');
            $table->decimal('proteins', 5, 2)->nullable()->after('calories');
            $table->decimal('fats', 5, 2)->nullable()->after('proteins');
            $table->decimal('carbohydrates', 5, 2)->nullable()->after('fats');
        });
    }

    public function down(): void
    {
        Schema::table('dish_addons', function (Blueprint $table) {
            $table->dropColumn([
                'calories',
                'proteins',
                'fats',
                'carbohydrates',
            ]);
        });
    }
};
