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
        Schema::create('constructor_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('constructor_category_id')->constrained('constructor_categories')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->string('weight_volume')->nullable();
            $table->integer('calories')->nullable();
            $table->decimal('proteins', 5, 2)->nullable();
            $table->decimal('fats', 5, 2)->nullable();
            $table->decimal('carbohydrates', 5, 2)->nullable();
            $table->decimal('fiber', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constructor_products');
    }
};
