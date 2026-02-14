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
            if (! Schema::hasColumn('phone_verifications', 'channel')) {
                $table->string('channel')->default(PhoneVerificationChannel::Sms->value)->after('request_id');
                $table->index('channel');
            }
            if (! Schema::hasColumn('phone_verifications', 'status')) {
                $table->string('status')->default(PhoneVerificationStatus::Pending->value)->after('channel');
                $table->index('status');
            }
            if (! Schema::hasColumn('phone_verifications', 'metadata')) {
                $table->json('metadata')->nullable()->after('code');
            }
        });
    }

    public function down(): void
    {
        // Leave columns in place; this migration is for fixing missing columns only.
    }
};
