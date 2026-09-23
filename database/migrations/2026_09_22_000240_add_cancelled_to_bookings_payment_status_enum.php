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
            DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_payment_status_check");
            DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_payment_status_check CHECK (payment_status IN ('unpaid', 'partial', 'paid', 'refunded', 'cancelled'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('unpaid', 'partial', 'paid', 'refunded', 'cancelled') NOT NULL DEFAULT 'unpaid'");
        }
        // SQLite stores enums as text and already accepts the new value.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_payment_status_check");
            DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_payment_status_check CHECK (payment_status IN ('unpaid', 'partial', 'paid', 'refunded'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid'");
        }
    }
};
