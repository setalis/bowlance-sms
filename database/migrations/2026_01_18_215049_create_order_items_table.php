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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Тип позиции: dish или bowl
            $table->string('item_type'); // 'dish' или 'bowl'
            $table->foreignId('dish_id')->nullable()->constrained()->nullOnDelete();

            // Информация о товаре (сохраняем на момент заказа)
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);

            // Пищевая ценность
            $table->integer('calories')->nullable();
            $table->decimal('proteins', 5, 2)->nullable();
            $table->decimal('fats', 5, 2)->nullable();
            $table->decimal('carbohydrates', 5, 2)->nullable();

            // Для боулов - сохраняем состав в JSON
            $table->json('bowl_products')->nullable();

            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
