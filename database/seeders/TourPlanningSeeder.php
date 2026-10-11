<?php

namespace Database\Seeders;

use App\Models\ResourceAllocation;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use Illuminate\Database\Seeder;

class TourPlanningSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['name' => 'Palawan Island Escape', 'type' => 'domestic', 'destination' => 'El Nido, Palawan', 'price' => 18900, 'duration_days' => 4, 'slots' => 24, 'description' => 'Island hopping, limestone lagoons, and a relaxed coastal stay.'],
            ['name' => 'Cebu Bluewater Weekend', 'type' => 'domestic', 'destination' => 'Cebu, Philippines', 'price' => 12400, 'duration_days' => 3, 'slots' => 18, 'description' => 'A compact beach and heritage itinerary for weekend travelers.'],
            ['name' => 'Batanes Slow Travel', 'type' => 'domestic', 'destination' => 'Basco, Batanes', 'price' => 21600, 'duration_days' => 5, 'slots' => 12, 'description' => 'Quiet roads, stone houses, and a guided northern island experience.'],
            ['name' => 'Bali Culture & Coast', 'type' => 'international', 'destination' => 'Bali, Indonesia', 'price' => 27900, 'duration_days' => 5, 'slots' => 20, 'description' => 'Temples, rice terraces, and a balanced coastal escape.'],
            ['name' => 'Seoul City Lights', 'type' => 'international', 'destination' => 'Seoul, South Korea', 'price' => 34800, 'duration_days' => 6, 'slots' => 16, 'description' => 'Markets, neighborhoods, food trails, and a guided city highlights route.'],
            ['name' => 'Kyoto Heritage Route', 'type' => 'international', 'destination' => 'Kyoto, Japan', 'price' => 42600, 'duration_days' => 7, 'slots' => 14, 'description' => 'A slower cultural route through shrines, gardens, and old-town districts.'],
        ];

        foreach ($packages as $index => $data) {
            $package = TourPackage::updateOrCreate(['name' => $data['name']], array_merge($data, ['status' => 'published']));
            $date = today()->addDays(7 + ($index * 4))->toDateString();
            $schedule = TourSchedule::updateOrCreate(
                ['tour_package_id' => $package->id, 'schedule_date' => $date],
                ['starts_at' => '08:30', 'ends_at' => '17:30', 'location' => $data['destination'], 'capacity' => $data['slots'], 'status' => 'confirmed', 'notes' => 'Published package departure.']
            );

            ResourceAllocation::firstOrCreate(
                ['tour_schedule_id' => $schedule->id, 'resource_type' => 'transport', 'resource_name' => 'Tour van / airport transfer'],
                ['quantity' => 1, 'status' => 'confirmed', 'notes' => 'Primary transport for the departure.']
            );
            ResourceAllocation::firstOrCreate(
                ['tour_schedule_id' => $schedule->id, 'resource_type' => 'guide', 'resource_name' => 'Destination guide'],
                ['quantity' => 1, 'status' => 'reserved', 'notes' => 'Local guide allocation.']
            );
        }
    }
}
