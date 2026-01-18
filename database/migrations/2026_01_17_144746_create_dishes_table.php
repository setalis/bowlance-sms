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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->foreignId('dish_category_id')->constrained('dish_categories')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('weight_volume')->nullable();
            $table->integer('calories')->nullable();
            $table->decimal('proteins', 5, 2)->nullable();
            $table->decimal('fats', 5, 2)->nullable();
            $table->decimal('carbohydrates', 5, 2)->nullable();
            $table->decimal('fiber', 5, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
