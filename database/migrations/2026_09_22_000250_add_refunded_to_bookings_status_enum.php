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
            DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_status_check");
            DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_status_check CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed', 'refunded'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'refunded') NOT NULL DEFAULT 'pending'");
        }
        // SQLite stores enums as text and already accepts the new value.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_status_check");
            DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_status_check CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed'))");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        }
    }
};