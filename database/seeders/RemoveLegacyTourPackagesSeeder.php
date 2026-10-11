<?php

namespace Database\Seeders;

use App\Models\TourPackage;
use Illuminate\Database\Seeder;

class RemoveLegacyTourPackagesSeeder extends Seeder
{
    public function run(): void
    {
        TourPackage::whereIn('name', [
            'Boracay Island Escape',
            'Palawan Island Discovery',
        ])->delete();
    }
}
