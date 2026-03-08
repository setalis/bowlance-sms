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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('delivery_city', 100)->nullable()->after('address');
            $table->string('delivery_street', 255)->nullable()->after('delivery_city');
            $table->string('delivery_house', 50)->nullable()->after('delivery_street');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['delivery_city', 'delivery_street', 'delivery_house']);
        });
    }
};
