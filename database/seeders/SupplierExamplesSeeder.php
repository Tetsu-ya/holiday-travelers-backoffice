<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierExamplesSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::whereIn('name', [
            'IslandLink Transport',
            'Blue Horizontal Hotel',
        ])->delete();

        $examples = [
            [
                'name' => 'Palawan Bay Resort',
                'category' => 'hotel',
                'phone' => '+63 917 642 1805',
                'location' => 'El Nido, Palawan',
                'base_rate' => 5200,
                'reliability_rating' => 4.8,
                'agreement_valid_until' => '2027-01-31',
                'perks_inclusions' => 'Breakfast, airport transfer, and 10% guest discount',
            ],
            [
                'name' => 'Boracay Pearl Hotel',
                'category' => 'hotel',
                'phone' => '+63 998 735 2401',
                'location' => 'Boracay, Aklan',
                'base_rate' => 6100,
                'reliability_rating' => 4.7,
                'agreement_valid_until' => '2026-12-20',
                'perks_inclusions' => 'Express check-in, welcome drinks, and beach access',
            ],
            [
                'name' => 'Cebu Vista Hotel',
                'category' => 'hotel',
                'phone' => '+63 905 418 7632',
                'location' => 'Cebu City, Cebu',
                'base_rate' => 4300,
                'reliability_rating' => 4.5,
                'agreement_valid_until' => '2027-02-28',
                'perks_inclusions' => 'Breakfast buffet, Wi-Fi, and late checkout',
            ],
            [
                'name' => 'Coastal Shuttle Services',
                'category' => 'transport',
                'phone' => '+63 917 284 5106',
                'location' => 'Puerto Princesa, Palawan',
                'base_rate' => 1800,
                'reliability_rating' => 4.6,
                'agreement_valid_until' => '2027-01-15',
                'perks_inclusions' => 'Hotel pickup, luggage assistance, and flight monitoring',
            ],
            [
                'name' => 'Cebu Island Transfers',
                'category' => 'transport',
                'phone' => '+63 926 503 8174',
                'location' => 'Cebu City, Cebu',
                'base_rate' => 1500,
                'reliability_rating' => 4.4,
                'agreement_valid_until' => '2026-11-30',
                'perks_inclusions' => 'Private vehicle, meet-and-greet, and bottled water',
            ],
            [
                'name' => 'Bohol Travel Transport',
                'category' => 'transport',
                'phone' => '+63 917 830 2265',
                'location' => 'Tagbilaran, Bohol',
                'base_rate' => 2100,
                'reliability_rating' => 4.5,
                'agreement_valid_until' => '2027-03-31',
                'perks_inclusions' => 'Air-conditioned van, driver-guide, and flexible stops',
            ],
            [
                'name' => 'El Nido Island Hopping Tours',
                'category' => 'tour_guide',
                'phone' => '+63 917 555 0142',
                'location' => 'El Nido, Palawan',
                'base_rate' => 4500,
                'reliability_rating' => 4.8,
                'agreement_valid_until' => '2026-12-31',
                'perks_inclusions' => 'Private boat option, snorkeling gear, and lunch included',
            ],
            [
                'name' => 'Coron Reef & Kayak Adventures',
                'category' => 'tour_guide',
                'phone' => '+63 998 246 7018',
                'location' => 'Coron, Palawan',
                'base_rate' => 3800,
                'reliability_rating' => 4.6,
                'agreement_valid_until' => '2027-03-15',
                'perks_inclusions' => 'Kayak rental, safety briefing, and hotel pickup',
            ],
            [
                'name' => 'Bohol Countryside Experiences',
                'category' => 'tour_guide',
                'phone' => '+63 905 331 4820',
                'location' => 'Tagbilaran, Bohol',
                'base_rate' => 3200,
                'reliability_rating' => 4.5,
                'agreement_valid_until' => '2027-01-30',
                'perks_inclusions' => 'Local guide, entrance fees, and complimentary bottled water',
            ],
            [
                'name' => 'Bamboo Garden Restaurant',
                'category' => 'other',
                'phone' => '+63 917 884 2106',
                'location' => 'Puerto Princesa, Palawan',
                'base_rate' => 850,
                'reliability_rating' => 4.7,
                'agreement_valid_until' => '2026-11-30',
                'perks_inclusions' => '10% discount for guests and priority reservations',
            ],
            [
                'name' => 'Island Souvenir House',
                'category' => 'other',
                'phone' => '+63 926 410 5523',
                'location' => 'Boracay, Aklan',
                'base_rate' => 500,
                'reliability_rating' => 4.3,
                'agreement_valid_until' => '2027-02-28',
                'perks_inclusions' => '10% guest discount and complimentary gift wrapping',
            ],
            [
                'name' => 'Vista Airport Transfer Services',
                'category' => 'other',
                'phone' => '+63 917 610 3398',
                'location' => 'Cebu City, Cebu',
                'base_rate' => 1200,
                'reliability_rating' => 4.6,
                'agreement_valid_until' => '2026-12-15',
                'perks_inclusions' => 'Meet-and-greet service, flight monitoring, and bottled water',
            ],
        ];

        foreach ($examples as $example) {
            Supplier::updateOrCreate(['name' => $example['name']], array_merge($example, [
                'status' => 'active',
            ]));
        }
    }
}
