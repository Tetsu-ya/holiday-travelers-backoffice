<?php

namespace Database\Seeders;

use App\Models\StaffAssignment;
use App\Models\BookingStaffAssignment;
use Illuminate\Database\Seeder;

class ResetStaffAssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        StaffAssignment::query()->delete();
        BookingStaffAssignment::query()->delete();
    }
}
