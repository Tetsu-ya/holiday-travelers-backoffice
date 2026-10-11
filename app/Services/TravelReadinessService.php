<?php

namespace App\Services;

use App\Models\TourSchedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TravelReadinessService
{
    public function assess(TourSchedule $schedule): array
    {
        $destination = $schedule->destination ?: $schedule->tourPackage?->destination ?: $schedule->location;
        $trafficDestination = $schedule->location ?: $destination;
        $date = $schedule->schedule_date->format('Y-m-d');
        $weather = $this->weather($destination, $date);
        $traffic = $this->traffic($trafficDestination, $schedule);
        $weatherScore = $this->weatherScore($weather);
        $trafficScore = $this->trafficScore($traffic);
        $overall = $weatherScore === null || $trafficScore === null ? null : min($weatherScore, $trafficScore);

        return [
            'schedule_id' => $schedule->id,
            'package' => $schedule->tourPackage?->name,
            'destination' => $destination,
            'date' => $date,
            'date_display' => $schedule->schedule_date->format('d M Y'),
            'weather' => $weather,
            'traffic' => $traffic,
            'weather_score' => $weatherScore,
            'traffic_score' => $trafficScore,
            'overall' => $overall,
            'recommendation' => $this->recommendation($overall, $weather, $traffic),
            'checked_at' => now()->toIso8601String(),
        ];
    }

    private function weather(?string $destination, string $date): array
    {
        if (! $destination) {
            return ['status' => 'unavailable', 'reason' => 'destination_missing', 'summary' => 'No destination is set for this departure.'];
        }

        $tourDate = \Illuminate\Support\Carbon::parse($date)->startOfDay();
        if ($tourDate->lt(today())) {
            return ['status' => 'unavailable', 'reason' => 'forecast_date_passed', 'summary' => 'A forecast is not available for a tour date that has already passed.'];
        }

        if ($tourDate->gt(today()->addDays(15))) {
            return ['status' => 'unavailable', 'reason' => 'forecast_out_of_range', 'summary' => 'A reliable daily forecast is available only within 15 days of departure. Check again closer to the tour date.'];
        }

        try {
            $geo = $this->geocode($destination);
        } catch (\Throwable $exception) {
            Log::warning('Travel readiness destination lookup failed.', [
                'exception' => get_class($exception),
                'destination' => $destination,
            ]);

            return ['status' => 'unavailable', 'reason' => 'provider_error', 'summary' => 'The weather location service could not be reached. Try checking again later.'];
        }

        if (! $geo) {
            return ['status' => 'unavailable', 'reason' => 'destination_not_found', 'summary' => 'Destination not recognized or ambiguous. Add a specific city, region, and country to the tour destination.'];
        }

        try {
            $forecast = Http::withOptions($this->httpOptions())->timeout(8)->get(config('services.weather.base_url'), [
                'latitude' => $geo['latitude'], 'longitude' => $geo['longitude'],
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max,wind_speed_10m_max',
                'timezone' => 'auto', 'start_date' => $date, 'end_date' => $date,
            ])->throw()->json();

            if (($forecast['daily']['time'][0] ?? null) !== $date) {
                return ['status' => 'unavailable', 'reason' => 'forecast_missing', 'summary' => 'The weather provider did not return a forecast for this departure date.'];
            }

            $daily = $forecast['daily'];
            $values = [
                $daily['weather_code'][0] ?? null,
                $daily['precipitation_probability_max'][0] ?? null,
                $daily['wind_speed_10m_max'][0] ?? null,
                $daily['temperature_2m_max'][0] ?? null,
                $daily['temperature_2m_min'][0] ?? null,
            ];
            if (in_array(null, $values, true) || count(array_filter($values, 'is_numeric')) !== count($values)) {
                return ['status' => 'unavailable', 'reason' => 'forecast_incomplete', 'summary' => 'The weather provider returned incomplete data for this departure date.'];
            }

            $code = (int) $daily['weather_code'][0];
            $rain = (int) $daily['precipitation_probability_max'][0];
            $wind = (float) $daily['wind_speed_10m_max'][0];

            return [
                'status' => 'available', 'summary' => $this->weatherSummary($code, $rain, $wind),
                'code' => $code, 'rain_probability' => $rain, 'wind_kmh' => round($wind, 1),
                'high' => $daily['temperature_2m_max'][0],
                'low' => $daily['temperature_2m_min'][0],
                'provider' => 'Open-Meteo',
                'location' => $this->locationLabel($geo),
            ];
        } catch (\Throwable $exception) {
            Log::warning('Travel readiness weather request failed.', [
                'message' => $exception->getMessage(),
                'destination' => $destination,
                'date' => $date,
            ]);

            return ['status' => 'unavailable', 'reason' => 'provider_error', 'summary' => 'The weather forecast provider could not be reached. Try checking again later.'];
        }
    }

    private function traffic(?string $destination, TourSchedule $schedule): array
    {
        if (! $destination) {
            return ['status' => 'unavailable', 'reason' => 'destination_missing', 'summary' => 'No destination is set for this departure.'];
        }

        $origin = trim((string) ($schedule->traffic_origin ?: config('services.tomtom.origin')));
        if ($origin === '') {
            return ['status' => 'unavailable', 'reason' => 'origin_missing', 'summary' => 'Set a specific road origin or meeting point to check traffic for this tour.'];
        }

        if (! config('services.tomtom.key')) {
            return ['status' => 'unavailable', 'reason' => 'provider_not_configured', 'summary' => 'Live traffic is not configured. Add a TomTom API key to check road conditions.'];
        }

        $weatherDestination = $schedule->destination ?: $schedule->tourPackage?->destination ?: $destination;
        $departure = $schedule->schedule_date->copy()->setTimeFromTimeString($schedule->starts_at ?: '08:00');

        try {
            $destinationGeography = $this->geocode($weatherDestination) ?: $this->geocode($destination);
            if (! $destinationGeography) {
                return ['status' => 'unavailable', 'reason' => 'location_not_found', 'summary' => 'The tour destination could not be resolved to a location. Add a specific city, region, and country before checking traffic.'];
            }

            $originGeo = $this->resolveRouteOrigin($origin, $destinationGeography);
            if (! $originGeo) {
                return ['status' => 'unavailable', 'reason' => 'origin_not_found', 'summary' => 'The configured road origin could not be resolved. Use a specific city, address, airport, or meeting point.'];
            }

            $countryCode = $destinationGeography['country_code'] ?? null;
            $destinationGeo = $this->geocodeTrafficLocation($destination, $countryCode) ?: $destinationGeography;

            $locationString = $originGeo['latitude'] . ',' . $originGeo['longitude'] . ':' . $destinationGeo['latitude'] . ',' . $destinationGeo['longitude'];
            $routeResponse = Http::withOptions($this->httpOptions())->timeout(10)
                ->get(config('services.tomtom.routing_url') . '/' . $locationString . '/json', [
                    'key' => config('services.tomtom.key'), 'traffic' => 'true',
                    'computeTravelTimeFor' => 'all', 'departAt' => $departure->toIso8601String(),
                    'travelMode' => 'car', 'routeType' => 'fastest',
                ]);

            if ($routeResponse->failed()) {
                $routeError = strtoupper((string) $routeResponse->json('detailedError.message'));
                if (str_contains($routeError, 'NO_ROUTE_FOUND')) {
                    return ['status' => 'unavailable', 'reason' => 'route_not_found', 'summary' => 'TomTom could not find a drivable route for these locations. Check the road origin and tour meeting point.'];
                }

                if (in_array($routeResponse->status(), [401, 403], true) || $routeResponse->status() === 429 || $routeResponse->status() === 400) {
                    return ['status' => 'unavailable', 'reason' => 'provider_error', 'summary' => 'TomTom could not provide a traffic estimate. Check the API configuration or try again later.'];
                }

                $routeResponse->throw();
            }

            $response = $routeResponse->json('routes.0');
            if (! $response) {
                return ['status' => 'unavailable', 'reason' => 'route_missing', 'summary' => 'TomTom returned no route for these locations. Check the road origin and tour meeting point.'];
            }

            $summary = $response['summary'] ?? [];
            $duration = (int) ($summary['travelTimeInSeconds'] ?? 0);
            $static = (int) ($summary['noTrafficTravelTimeInSeconds'] ?? $duration);
            if ($duration <= 0 || $static <= 0) {
                return ['status' => 'unavailable', 'reason' => 'traffic_data_missing', 'summary' => 'TomTom returned incomplete route timing data, so traffic could not be assessed.'];
            }
            $delay = $static > 0 ? round((($duration - $static) / $static) * 100) : 0;
            $status = $delay >= 35 ? 'heavy' : ($delay >= 15 ? 'moderate' : 'light');

            $originLabel = $this->locationLabel($originGeo);
            $destinationLabel = $this->locationLabel($destinationGeo);

            return [
                'status' => 'available',
                'provider' => 'TomTom',
                'level' => $status,
                'delay_percent' => max(0, $delay),
                'duration_minutes' => round($duration / 60),
                'origin' => $originLabel,
                'destination' => $destinationLabel,
                'origin_precision' => $originGeo['precision'] ?? 'city',
                'destination_precision' => $destinationGeo['precision'] ?? 'city',
                'departure_at' => $departure->toIso8601String(),
                'summary' => ucfirst($status) . ' road traffic estimate from ' . $originLabel . ' to ' . $destinationLabel . '.',
            ];
        } catch (\Throwable $exception) {
            Log::warning('Travel readiness TomTom request failed.', [
                'exception' => get_class($exception),
                'destination' => $destination,
            ]);

            return ['status' => 'unavailable', 'reason' => 'provider_error', 'summary' => 'The traffic provider could not be reached. Try checking again later.'];
        }
    }

    private function geocode(string $place): ?array
    {
        foreach ($this->geocodeCandidates($place) as $candidate) {
            $result = Http::withOptions($this->httpOptions())->timeout(8)->get(config('services.weather.geocoding_url'), [
                'name' => $candidate, 'count' => 10, 'language' => 'en', 'format' => 'json',
            ])->throw()->json('results');

            if ($result) {
                $qualifiers = $this->locationQualifiers($place);
                foreach ($result as $match) {
                    $matchedQualifiers = array_filter($qualifiers, fn ($qualifier) => $this->containsLocationText($match, $qualifier));
                    if (count($matchedQualifiers) === count($qualifiers)) {
                        return $match;
                    }
                }

                if ($qualifiers === []) {
                    return count($result) === 1 ? $result[0] : null;
                }
            }
        }

        return null;
    }

    private function geocodeTrafficLocation(string $place, ?string $countryCode = null): ?array
    {
        try {
            $parameters = [
                'key' => config('services.tomtom.key'),
                'limit' => 10,
                'idxSet' => 'POI,Addr',
                'typeahead' => 'false',
            ];
            if ($countryCode) {
                $parameters['countrySet'] = $countryCode;
            }

            $qualifiers = $this->locationQualifiers($place);
            $parts = array_values(array_filter(array_map('trim', explode(',', $place))));
            $searchCandidates = count($parts) > 1 ? [$parts[0], $place] : [$place];

            foreach (array_unique($searchCandidates) as $candidate) {
                $results = Http::withOptions($this->httpOptions())->timeout(8)
                    ->get(rtrim(config('services.tomtom.search_url'), '/') . '/' . rawurlencode($candidate) . '.json', $parameters)
                    ->throw()->json('results', []);

                foreach ($results as $result) {
                    $address = $result['address'] ?? [];
                    $position = $result['position'] ?? [];
                    if (! isset($position['lat'], $position['lon'])) {
                        continue;
                    }

                    $location = [
                        'name' => $result['poi']['name'] ?? $address['freeformAddress'] ?? $place,
                        'latitude' => $position['lat'],
                        'longitude' => $position['lon'],
                        'country' => $address['country'] ?? null,
                        'country_code' => $address['countryCode'] ?? null,
                        'admin1' => $address['countrySubdivision'] ?? null,
                        'admin2' => $address['municipality'] ?? null,
                        'admin3' => $address['municipalitySubdivision'] ?? null,
                        'precision' => in_array($result['type'] ?? '', ['POI', 'Point Address', 'Street'], true) ? 'place' : 'city',
                    ];
                    $matchedQualifiers = array_filter($qualifiers, fn ($qualifier) => $this->containsLocationText($location, $qualifier));
                    $countryMatches = ! $countryCode || strtoupper($location['country_code'] ?? '') === strtoupper($countryCode);
                    $nameMatches = $this->matchesPlaceName($location['name'], $place);
                    $requestedTypeMatches = $this->matchesPlaceType($result, $place);
                    $isAddress = in_array($result['type'] ?? '', ['Point Address', 'Street'], true);

                    if ($countryMatches && ($nameMatches || $requestedTypeMatches || ($isAddress && count($matchedQualifiers) === count($qualifiers)))) {
                        return $location;
                    }
                }
            }
        } catch (\Throwable $exception) {
            Log::notice('TomTom place search failed; falling back to city geocoding.', [
                'exception' => get_class($exception),
                'place' => $place,
            ]);
        }

        $location = $this->geocode($place);
        if ($location) {
            $location['precision'] = 'city';
        }

        return $location;
    }

    private function locationQualifiers(string $place): array
    {
        $parts = array_values(array_filter(array_map('trim', explode(',', $place))));
        array_shift($parts);

        return $parts;
    }

    private function containsLocationText(array $location, string $text): bool
    {
        $haystack = implode(' ', array_filter([
            $location['country'] ?? null,
            $location['admin1'] ?? null,
            $location['admin2'] ?? null,
            $location['admin3'] ?? null,
        ]));
        $normalizedText = mb_strtolower(trim($text));
        $countryAliases = [
            'uk' => 'gb',
            'usa' => 'us',
            'u.s.' => 'us',
            'u.s.a.' => 'us',
            'uae' => 'ae',
        ];
        $countryCode = $countryAliases[$normalizedText] ?? (strlen($normalizedText) === 2 ? $normalizedText : null);

        if ($countryCode && strtolower($location['country_code'] ?? '') === $countryCode) {
            return true;
        }

        return mb_stripos($haystack, $text) !== false;
    }

    private function matchesPlaceName(string $resultName, string $place): bool
    {
        $queryName = trim(explode(',', $place)[0]);
        $normalize = static function (string $value): string {
            $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            $value = $ascii === false ? $value : $ascii;

            return trim(preg_replace('/[^a-z0-9]+/i', ' ', mb_strtolower($value)));
        };

        return $queryName !== '' && $normalize($queryName) === $normalize($resultName);
    }

    private function matchesPlaceType(array $result, string $place): bool
    {
        $requestedType = strtolower(explode(',', $place)[0]);
        $classification = strtoupper((string) data_get($result, 'poi.classifications.0.code'));

        return (str_contains($requestedType, 'station') && str_contains($classification, 'STATION'))
            || (str_contains($requestedType, 'airport') && str_contains($classification, 'AIRPORT'))
            || (str_contains($requestedType, 'port') && str_contains($classification, 'PORT'));
    }

    private function locationLabel(array $location): string
    {
        return implode(', ', array_values(array_unique(array_filter([
            $location['name'] ?? null,
            $location['admin1'] ?? null,
            $location['country'] ?? null,
        ]))));
    }

    private function geocodeCandidates(string $place): array
    {
        $place = trim(preg_replace('/\s+/', ' ', (string) $place));
        if ($place === '') {
            return [];
        }

        $normalized = trim(str_replace(['–', '—'], '-', $place));
        $candidates = [$normalized];

        $withoutBranding = preg_replace('/\b(?:adventure|escape|experience|island|journey|package|resort|tour|travel|vacation)\b/i', '', $normalized);
        $withoutBranding = trim(preg_replace('/\s+/', ' ', (string) $withoutBranding), " ,-");
        if ($withoutBranding !== '' && $withoutBranding !== $normalized) {
            $candidates[] = $withoutBranding;
        }

        $parts = array_values(array_filter(array_map('trim', explode(',', $normalized))));
        if (count($parts) > 1) {
            foreach ($parts as $part) {
                if ($part !== '') {
                    $candidates[] = $part;
                }
            }
        }

        $candidates = array_values(array_unique(array_filter($candidates, fn ($candidate) => is_string($candidate) && trim($candidate) !== '')));

        return $candidates;
    }

    private function httpOptions(): array
    {
        $options = [];
        $caBundle = config('services.http.ca_bundle');
        if ($caBundle && is_file($caBundle)) {
            $options['verify'] = $caBundle;
        }
        return $options;
    }

    private function weatherScore(array $weather): ?int
    {
        if (($weather['status'] ?? null) !== 'available') return null;
        $score = 100;
        if (($weather['rain_probability'] ?? 0) >= 70) $score -= 30;
        elseif (($weather['rain_probability'] ?? 0) >= 45) $score -= 15;
        if (($weather['wind_kmh'] ?? 0) >= 45) $score -= 35;
        elseif (($weather['wind_kmh'] ?? 0) >= 30) $score -= 15;
        if (($weather['code'] ?? 0) >= 95) $score -= 50;
        elseif (($weather['code'] ?? 0) >= 80) $score -= 20;
        return max(0, $score);
    }

    private function trafficScore(array $traffic): ?int
    {
        if (($traffic['status'] ?? null) !== 'available') return null;
        return match ($traffic['level'] ?? 'light') { 'heavy' => 35, 'moderate' => 70, default => 95 };
    }

    private function recommendation(?int $overall, array $weather, array $traffic): array
    {
        if ($overall === null) {
            $unavailable = array_filter([$weather, $traffic], fn ($result) => ($result['status'] ?? null) !== 'available');
            $summary = implode(' ', array_column($unavailable, 'summary'));

            return ['level' => 'setup', 'label' => 'Unable to confirm travel readiness', 'summary' => $summary ?: 'One or more travel data sources did not return usable information.'];
        }
        $level = $overall >= 80 ? 'safe' : ($overall >= 55 ? 'caution' : 'review');
        return ['level' => $level, 'label' => match ($level) { 'safe' => 'Safe to travel', 'caution' => 'Travel with caution', default => 'Review before departure' }, 'summary' => $level === 'safe' ? 'Conditions look suitable for the scheduled tour.' : 'Review the conditions and consider adjusting the departure plan before travel.'];
    }

    private function weatherSummary(int $code, int $rain, float $wind): string
    {
        $condition = $code >= 95 ? 'thunderstorm risk' : ($code >= 80 ? 'rain showers' : ($code >= 50 ? 'cloudy conditions' : 'generally clear conditions'));
        return ucfirst($condition) . ", {$rain}% rain chance, wind up to " . round($wind) . ' km/h.';
    }

    private function resolveRouteOrigin(string $origin, array $destinationGeo): ?array
    {
        if (! trim($origin)) {
            return null;
        }

        $countryCode = $destinationGeo['country_code'] ?? null;
        $resolved = $this->geocodeTrafficLocation($origin, $countryCode);
        if ($resolved) {
            return $resolved;
        }

        $candidates = array_values(array_filter(array_map('trim', explode(',', $origin))));
        foreach ($candidates as $candidate) {
            $result = $this->geocode($candidate);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    private function seconds(?string $duration): int
    {
        return $duration && preg_match('/([0-9]+(?:\.[0-9]+)?)s/', $duration, $matches) ? (int) round((float) $matches[1]) : 0;
    }
}
