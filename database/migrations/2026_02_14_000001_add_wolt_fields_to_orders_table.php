<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('wolt_delivery_id')->nullable()->after('status');
            $table->string('wolt_status')->nullable()->after('wolt_delivery_id');
            $table->text('wolt_tracking_url')->nullable()->after('wolt_status');
            $table->json('wolt_last_payload')->nullable()->after('wolt_tracking_url');

            $table->index('wolt_delivery_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['wolt_delivery_id']);
            $table->dropColumn([
                'wolt_delivery_id',
                'wolt_status',
                'wolt_tracking_url',
                'wolt_last_payload',
            ]);
        });
    }
};
