<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constructor_products', function (Blueprint $table) {
            $table->unsignedInteger('poster_modification_id')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('constructor_products', function (Blueprint $table) {
            $table->dropColumn('poster_modification_id');
        });
    }
};
