<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            if (! Schema::hasColumn('phone_verifications', 'channel')) {
                $table->string('channel')->default('sms')->after('phone');
            }

            $table->string('telegram_token')->nullable()->unique()->after('channel');
            $table->unsignedBigInteger('telegram_chat_id')->nullable()->after('telegram_token');
        });
    }

    public function down(): void
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            if (Schema::hasColumn('phone_verifications', 'telegram_token')) {
                $table->dropUnique(['telegram_token']);
                $table->dropColumn(['telegram_token', 'telegram_chat_id']);
            }
        });
    }
};
