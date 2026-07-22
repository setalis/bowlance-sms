<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dish_dish_addon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('dishes')->cascadeOnDelete();
            $table->foreignId('dish_addon_id')->constrained('dish_addons')->cascadeOnDelete();
            $table->unsignedInteger('poster_modification_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['dish_id', 'dish_addon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_dish_addon');
    }
};
