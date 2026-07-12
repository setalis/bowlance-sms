<?php

use App\Enums\ConstructorType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('constructor_categories', function (Blueprint $table) {
            $table->string('type')->default(ConstructorType::Bowl->value)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('constructor_categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
