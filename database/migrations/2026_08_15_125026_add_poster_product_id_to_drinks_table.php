<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drinks', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->unsignedInteger('poster_product_id')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('drinks', function (Blueprint $table) {
            $table->dropColumn('poster_product_id');
            $table->string('name')->nullable(false)->change();
        });
    }
};
