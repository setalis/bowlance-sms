<?php

use App\Enums\PhoneVerificationChannel;
use App\Enums\PhoneVerificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->string('channel')->default(PhoneVerificationChannel::Sms->value)->after('request_id');
            $table->string('status')->default(PhoneVerificationStatus::Pending->value)->after('channel');
            $table->json('metadata')->nullable()->after('code');

            $table->index('channel');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('phone_verifications', function (Blueprint $table) {
            $table->dropIndex(['channel']);
            $table->dropIndex(['status']);
            $table->dropColumn(['channel', 'status', 'metadata']);
        });
    }
};
