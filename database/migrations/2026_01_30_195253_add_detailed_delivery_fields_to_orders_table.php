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
            // Разбиваем delivery_address на детальные поля
            $table->string('entrance', 50)->nullable()->after('delivery_address');
            $table->string('floor', 50)->nullable()->after('entrance');
            $table->string('apartment', 50)->nullable()->after('floor');
            $table->string('intercom', 50)->nullable()->after('apartment');
            $table->text('courier_comment')->nullable()->after('intercom');
            $table->string('receiver_phone', 50)->nullable()->after('courier_comment');
            $table->boolean('leave_at_door')->default(false)->after('receiver_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'entrance',
                'floor',
                'apartment',
                'intercom',
                'courier_comment',
                'receiver_phone',
                'leave_at_door',
            ]);
        });
    }
};
