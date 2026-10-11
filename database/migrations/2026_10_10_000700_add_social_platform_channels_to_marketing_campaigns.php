<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE marketing_campaigns MODIFY channel ENUM('email', 'social_media', 'facebook', 'instagram', 'tiktok', 'referral', 'ads', 'events') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE marketing_campaigns DROP CONSTRAINT IF EXISTS marketing_campaigns_channel_check');
            DB::statement("ALTER TABLE marketing_campaigns ADD CONSTRAINT marketing_campaigns_channel_check CHECK (channel IN ('email', 'social_media', 'facebook', 'instagram', 'tiktok', 'referral', 'ads', 'events'))");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE marketing_campaigns SET channel = 'social_media' WHERE channel IN ('facebook', 'instagram', 'tiktok')");
            DB::statement("ALTER TABLE marketing_campaigns MODIFY channel ENUM('email', 'social_media', 'referral', 'ads', 'events') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement("UPDATE marketing_campaigns SET channel = 'social_media' WHERE channel IN ('facebook', 'instagram', 'tiktok')");
            DB::statement('ALTER TABLE marketing_campaigns DROP CONSTRAINT IF EXISTS marketing_campaigns_channel_check');
            DB::statement("ALTER TABLE marketing_campaigns ADD CONSTRAINT marketing_campaigns_channel_check CHECK (channel IN ('email', 'social_media', 'referral', 'ads', 'events'))");
        }
    }
};
