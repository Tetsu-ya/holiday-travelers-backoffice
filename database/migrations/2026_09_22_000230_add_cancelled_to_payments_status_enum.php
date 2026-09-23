<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'paid', 'partial', 'refunded', 'cancelled'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending', 'paid', 'partial', 'refunded', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
        // SQLite stores enums as text and already accepts the new value.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'paid', 'partial', 'refunded'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY status ENUM('pending', 'paid', 'partial', 'refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
