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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_city')->nullable()->after('delivery_address');
            $table->string('delivery_street')->nullable()->after('delivery_city');
            $table->string('delivery_house')->nullable()->after('delivery_street');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_city', 'delivery_street', 'delivery_house']);
        });
    }
};
