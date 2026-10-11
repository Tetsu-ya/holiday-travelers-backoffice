<?php

namespace Tests\Unit;

use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Services\TravelReadinessService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TravelReadinessServiceTest extends TestCase
{
    public function test_it_uses_a_city_fallback_and_returns_weather_and_traffic_data(): void
    {
        $date = today()->addDays(3)->format('Y-m-d');
        config([
            'services.tomtom.key' => 'test-key',
            'services.tomtom.origin' => 'Manila, Philippines',
        ]);

        Http::fake(function (Request $request) use ($date) {
            $host = parse_url($request->url(), PHP_URL_HOST);
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            if ($host === 'geocoding-api.open-meteo.com') {
                if ($query['name'] === 'El Nido, Palawan') {
                    return Http::response(['generationtime_ms' => 0]);
                }

                if ($query['name'] === 'El Nido Port, Palawan') {
                    return Http::response(['results' => [[
                        'name' => 'El Nido Port',
                        'latitude' => 11.194,
                        'longitude' => 119.405,
                        'country' => 'Philippines',
                        'country_code' => 'PH',
                        'admin1' => 'Mimaropa',
                        'admin2' => 'Province of Palawan',
                    ]]]);
                }

                $location = $query['name'] === 'Manila, Philippines'
                    ? ['name' => 'Manila', 'latitude' => 14.6042, 'longitude' => 120.9822, 'country' => 'Philippines', 'country_code' => 'PH', 'admin1' => 'National Capital Region']
                    : ['name' => 'El Nido', 'latitude' => 11.18583, 'longitude' => 119.39556, 'country' => 'Philippines', 'country_code' => 'PH', 'admin1' => 'Mimaropa', 'admin2' => 'Province of Palawan'];

                return Http::response(['results' => [$location]]);
            }

            if ($host === 'api.open-meteo.com') {
                return Http::response(['daily' => [
                    'time' => [$date],
                    'weather_code' => [2],
                    'precipitation_probability_max' => [20],
                    'wind_speed_10m_max' => [12.5],
                    'temperature_2m_max' => [30.0],
                    'temperature_2m_min' => [24.0],
                ]]);
            }

            if ($host === 'api.tomtom.com' && str_contains($request->url(), '/search/2/search/')) {
                $requestedPlace = rawurldecode(basename((string) parse_url($request->url(), PHP_URL_PATH), '.json'));
                $isOrigin = str_contains($requestedPlace, 'El Nido Port');

                return Http::response(['results' => [[
                    'type' => 'POI',
                    'poi' => ['name' => $isOrigin ? 'El Nido Port' : 'El Nido'],
                    'position' => [
                        'lat' => $isOrigin ? 11.194 : 11.18583,
                        'lon' => $isOrigin ? 119.405 : 119.39556,
                    ],
                    'address' => [
                        'country' => 'Philippines',
                        'countryCode' => 'PH',
                        'countrySubdivision' => 'Mimaropa',
                        'municipalitySubdivision' => 'El Nido',
                    ],
                ]]]);
            }

            if ($host === 'api.tomtom.com') {
                return Http::response(['routes' => [['summary' => [
                    'travelTimeInSeconds' => 600,
                    'noTrafficTravelTimeInSeconds' => 500,
                ]]]]);
            }

            return Http::response([], 404);
        });

        $result = app(TravelReadinessService::class)->assess($this->schedule($date, 'El Nido, Palawan', 'El Nido Port, Palawan'));

        $this->assertSame('available', $result['weather']['status']);
        $this->assertSame('El Nido, Mimaropa, Philippines', $result['weather']['location']);
        $this->assertSame(100, $result['weather_score']);
        $this->assertSame('available', $result['traffic']['status']);
        $this->assertSame('TomTom', $result['traffic']['provider']);
        $this->assertStringContainsString('El Nido Port, Mimaropa, Philippines', $result['traffic']['summary']);
        $this->assertSame(70, $result['traffic_score']);
        $this->assertSame(70, $result['overall']);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'name=El%20Nido'));
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/search/2/search/El%20Nido%20Port.json')
            && str_contains($request->url(), 'countrySet=PH'));
    }

    public function test_it_distinguishes_a_location_not_found_from_a_provider_failure(): void
    {
        $date = today()->addDays(3)->format('Y-m-d');
        Http::fake(fn () => Http::response(['generationtime_ms' => 0]));

        $result = app(TravelReadinessService::class)->assess($this->schedule($date, 'Unknown Somewhere'));

        $this->assertSame('destination_not_found', $result['weather']['reason']);
        $this->assertStringContainsString('Destination not recognized', $result['weather']['summary']);
    }

    public function test_it_reports_a_missing_drivable_route_separately_from_provider_errors(): void
    {
        $date = today()->addDays(3)->format('Y-m-d');
        config([
            'services.tomtom.key' => 'test-key',
            'services.tomtom.origin' => 'Manila, Philippines',
        ]);

        Http::fake(function (Request $request) use ($date) {
            $host = parse_url($request->url(), PHP_URL_HOST);
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            if ($host === 'geocoding-api.open-meteo.com') {
                $name = $query['name'];
                return Http::response(['results' => [[
                    'name' => $name === 'Manila, Philippines' ? 'Manila' : 'El Nido',
                    'latitude' => $name === 'Manila, Philippines' ? 14.6042 : 11.18583,
                    'longitude' => $name === 'Manila, Philippines' ? 120.9822 : 119.39556,
                    'country' => 'Philippines',
                    'country_code' => 'PH',
                    'admin1' => 'Mimaropa',
                    'admin2' => 'Province of Palawan',
                ]]]);
            }

            if ($host === 'api.open-meteo.com') {
                return Http::response(['daily' => [
                    'time' => [$date],
                    'weather_code' => [2],
                    'precipitation_probability_max' => [20],
                    'wind_speed_10m_max' => [12.5],
                    'temperature_2m_max' => [30.0],
                    'temperature_2m_min' => [24.0],
                ]]);
            }

            if ($host === 'api.tomtom.com') {
                return Http::response(['detailedError' => ['message' => 'NO_ROUTE_FOUND']], 400);
            }

            return Http::response([], 404);
        });

        $result = app(TravelReadinessService::class)->assess($this->schedule($date, 'El Nido, Palawan'));

        $this->assertSame('route_not_found', $result['traffic']['reason']);
        $this->assertStringContainsString('Check the road origin', $result['traffic']['summary']);
        $this->assertNull($result['traffic_score']);
        $this->assertNull($result['overall']);
        $this->assertSame('setup', $result['recommendation']['level']);
    }

    public function test_it_reports_weather_provider_failures_separately_from_unrecognized_destinations(): void
    {
        $date = today()->addDays(3)->format('Y-m-d');
        Http::fake(function (Request $request) {
            if (parse_url($request->url(), PHP_URL_HOST) === 'geocoding-api.open-meteo.com') {
                return Http::response(['results' => [[
                    'name' => 'El Nido',
                    'latitude' => 11.18583,
                    'longitude' => 119.39556,
                    'country' => 'Philippines',
                    'country_code' => 'PH',
                    'admin1' => 'Mimaropa',
                    'admin2' => 'Province of Palawan',
                ]]]);
            }

            return Http::response([], 503);
        });

        $result = app(TravelReadinessService::class)->assess($this->schedule($date, 'El Nido, Palawan'));

        $this->assertSame('provider_error', $result['weather']['reason']);
        $this->assertStringContainsString('forecast provider could not be reached', $result['weather']['summary']);
        $this->assertNull($result['weather_score']);
        $this->assertNull($result['overall']);
    }

    public function test_it_does_not_score_weather_outside_the_forecast_window(): void
    {
        Http::fake();

        $result = app(TravelReadinessService::class)->assess($this->schedule(today()->addDays(16)->format('Y-m-d'), 'El Nido, Palawan'));

        $this->assertSame('forecast_out_of_range', $result['weather']['reason']);
        $this->assertNull($result['weather_score']);
        Http::assertNotSent(fn (Request $request) => parse_url($request->url(), PHP_URL_HOST) === 'api.open-meteo.com');
    }

    public function test_it_does_not_invent_a_traffic_origin_when_one_is_not_configured(): void
    {
        config([
            'services.tomtom.key' => 'test-key',
            'services.tomtom.origin' => null,
        ]);
        Http::fake();

        $result = app(TravelReadinessService::class)->assess($this->schedule(today()->addDays(3)->format('Y-m-d'), 'El Nido, Palawan'));

        $this->assertSame('origin_missing', $result['traffic']['reason']);
        $this->assertNull($result['traffic_score']);
        $this->assertNull($result['overall']);
        $this->assertSame('setup', $result['recommendation']['level']);
    }

    public function test_it_uses_worldwide_region_and_country_qualifiers_to_choose_the_correct_city(): void
    {
        $date = today()->addDays(3)->format('Y-m-d');
        Http::fake(function (Request $request) use ($date) {
            $host = parse_url($request->url(), PHP_URL_HOST);

            if ($host === 'geocoding-api.open-meteo.com') {
                return Http::response(['results' => [
                    ['name' => 'Springfield', 'latitude' => 42.1, 'longitude' => -72.6, 'country' => 'United States', 'country_code' => 'US', 'admin1' => 'Massachusetts'],
                    ['name' => 'Springfield', 'latitude' => 39.78, 'longitude' => -89.65, 'country' => 'United States', 'country_code' => 'US', 'admin1' => 'Illinois'],
                ]]);
            }

            if ($host === 'api.open-meteo.com') {
                return Http::response(['daily' => [
                    'time' => [$date],
                    'weather_code' => [1],
                    'precipitation_probability_max' => [5],
                    'wind_speed_10m_max' => [10],
                    'temperature_2m_max' => [25],
                    'temperature_2m_min' => [14],
                ]]);
            }

            return Http::response([], 404);
        });

        $result = app(TravelReadinessService::class)->assess($this->schedule($date, 'Springfield, Illinois, United States'));

        $this->assertSame('available', $result['weather']['status']);
        $this->assertSame('Springfield, Illinois, United States', $result['weather']['location']);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'name=Springfield'));
    }

    private function schedule(string $date, string $destination, ?string $trafficOrigin = null): TourSchedule
    {
        $schedule = new TourSchedule([
            'schedule_date' => $date,
            'starts_at' => '08:00',
            'location' => $destination,
            'traffic_origin' => $trafficOrigin,
        ]);
        $schedule->setRelation('tourPackage', new TourPackage([
            'name' => 'Test tour',
            'destination' => $destination,
        ]));

        return $schedule;
    }
}
