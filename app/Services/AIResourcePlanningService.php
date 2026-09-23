<?php

namespace App\Services;

use App\Models\AiResourcePlan;
use App\Models\Booking;
use App\Models\MarketingCampaign;
use App\Models\Supplier;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

/**
 * Central service for the "AI-Assisted Resource Planning" module.
 *
 * Responsible for turning operational data (bookings, suppliers,
 * campaigns, partners) into structured recommendations. Each method
 * builds a data snapshot, optionally sends it to an LLM (Anthropic API,
 * configurable via .env AI_PROVIDER / AI_API_KEY) for a plain-language
 * summary, and persists the result to ai_resource_plans for the
 * AI Planning dashboard to display.
 */
class AIResourcePlanningService
{
    /**
     * Forecast expected demand per package for an upcoming period
     * using historical booking volume (simple moving average /
     * seasonality baseline; swap in a real model later).
     */
    public function forecastDemand(TourPackage $package): AiResourcePlan
    {
        $monthlyPax = Booking::where('tour_package_id', $package->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('travel_date', '>=', now()->subMonths(6)->startOfMonth())
            ->get(['travel_date', 'pax'])
            ->groupBy(fn ($booking) => $booking->travel_date->format('Y-m'))
            ->map(fn ($bookings, $month) => ['month' => $month, 'pax' => (int) $bookings->sum('pax')])
            ->sortKeys();

        $history = $monthlyPax->values();
        $weights = range(1, max(1, $history->count()));
        $weightedAverage = $history->isEmpty()
            ? 0
            : collect($history)->values()->reduce(
                fn ($total, $row, $index) => $total + ($row['pax'] * $weights[$index]),
                0
            ) / array_sum($weights);
        $firstPax = $history->first()['pax'] ?? 0;
        $lastPax = $history->last()['pax'] ?? 0;
        $trend = $firstPax > 0 ? (($lastPax - $firstPax) / $firstPax) : 0;
        $forecast = (int) round(max(0, $weightedAverage * (1 + ($trend * 0.35))));
        $recommendedSlots = max($forecast, (int) $package->slots);
        $capacityGap = max(0, $forecast - (int) $package->slots);
        $confidence = min(95, 55 + ($history->count() * 6));

        return $this->store('demand_forecast', $package->name, [
            'history' => $history->all(),
            'slots_available' => $package->slots,
        ], [
            'forecast_pax_next_month' => $forecast,
            'recommended_slots' => $recommendedSlots,
            'capacity_gap' => $capacityGap,
            'trend_percent' => round($trend * 100, 1),
        ], "Based on the latest six months of confirmed bookings, {$package->name} is projected to need approximately {$forecast} pax next month.", $confidence);
    }

    /**
     * Recommend the best supplier for a package based on rate,
     * reliability rating, and current status.
     */
    public function recommendSupplier(string $category, string $location = null): AiResourcePlan
    {
        $candidates = Supplier::where('category', $category)
            ->where('status', 'active')
            ->when($location, fn ($q) => $q->where('location', 'like', "%{$location}%"))
            ->orderByDesc('reliability_rating')
            ->orderBy('base_rate')
            ->limit(5)
            ->get(['id', 'name', 'base_rate', 'reliability_rating', 'location']);

        $best = $candidates->first();

        $confidence = $best ? min(95, 65 + ($candidates->count() * 5)) : 35;

        return $this->store('supplier_allocation', $category, [
            'category' => $category,
            'location' => $location,
        ], [
            'ranked_candidates' => $candidates,
            'top_pick_id' => $best?->id,
        ], $best ? "{$best->name} is the top-ranked {$category} supplier by reliability and cost." : 'No active suppliers found for this category.', $confidence);
    }

    /**
     * Suggest how marketing budget should be reallocated across
     * channels based on historical conversion performance.
     */
    public function recommendMarketingBudget(): AiResourcePlan
    {
        $campaigns = MarketingCampaign::selectRaw('channel, SUM(actual_spend) as spend, SUM(conversions) as conversions')
            ->groupBy('channel')
            ->get()
            ->map(function ($row) {
                $row->cost_per_conversion = $row->conversions > 0 ? round($row->spend / $row->conversions, 2) : null;
                return $row;
            })
            ->sortBy(fn ($row) => $row->cost_per_conversion === null ? PHP_FLOAT_MAX : $row->cost_per_conversion)
            ->values();

        $topChannel = $campaigns->first();

        $totalSpend = $campaigns->sum('spend');
        $allocationBase = $campaigns->sum(fn ($item) => $item->cost_per_conversion ? $totalSpend / $item->cost_per_conversion : 0);
        $rankedChannels = $campaigns->map(function ($channel) use ($totalSpend, $allocationBase) {
            $channel->recommended_share = $totalSpend > 0 && $channel->cost_per_conversion !== null
                ? round(($totalSpend / $channel->cost_per_conversion) / max(1, $allocationBase) * 100, 1)
                : 0;
            return $channel;
        });

        return $this->store('marketing_budget', 'all_channels', [
            'channel_performance' => $campaigns,
        ], [
            'ranked_channels' => $rankedChannels,
            'recommended_focus_channel' => $topChannel?->channel,
        ], $topChannel ? "The '{$topChannel->channel}' channel has the lowest cost per conversion and is recommended for increased budget." : 'Insufficient campaign data to generate a recommendation.', $topChannel ? 80 : 35);
    }

    /** Run the complete planning cycle used by the scheduler. */
    public function runAutomaticPlanning(): Collection
    {
        $plans = collect();

        TourPackage::whereIn('status', ['draft', 'published'])->orderBy('id')->get()->each(function ($package) use (&$plans) {
            $plan = $this->forecastDemand($package);
            $plans->push($plan);

            $gap = (int) data_get($plan->recommendation, 'capacity_gap', 0);
            if ($gap > 0) {
                $plans->push($this->store(
                    'anomaly_alert',
                    $package->name,
                    ['package_id' => $package->id, 'available_slots' => $package->slots],
                    ['alert' => 'capacity_shortfall', 'additional_slots_needed' => $gap],
                    "Capacity alert: {$package->name} may need {$gap} additional slot(s) next month.",
                    90
                ));
            }
        });

        Supplier::where('status', 'active')->select('category')->distinct()->pluck('category')->each(function ($category) use (&$plans) {
            $plans->push($this->recommendSupplier($category));
        });

        $plans->push($this->recommendMarketingBudget());

        TourSchedule::with(['tourPackage', 'allocations', 'assignments'])
            ->whereBetween('schedule_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->whereIn('status', ['planned', 'confirmed'])
            ->get()
            ->each(function ($schedule) use (&$plans) {
                $allocated = (int) $schedule->allocations->sum('quantity');
                $staffAssigned = $schedule->assignments->count();
                $issues = [];
                $subject = ($schedule->tourPackage?->name ?? "Schedule #{$schedule->id}") . ' · ' . $schedule->schedule_date->format('d M Y');

                if ($allocated < (int) $schedule->capacity) {
                    $issues[] = 'resource_shortfall';
                }
                if ($staffAssigned === 0) {
                    $issues[] = 'staff_missing';
                }

                if ($issues && ! AiResourcePlan::where('plan_type', 'anomaly_alert')
                    ->where('subject', $subject)
                    ->whereDate('created_at', now()->toDateString())
                    ->exists()) {
                    $plans->push($this->store(
                        'anomaly_alert',
                        $subject,
                        ['schedule_id' => $schedule->id, 'schedule_date' => $schedule->schedule_date?->format('Y-m-d')],
                        ['alert' => 'resource_readiness', 'issues' => $issues, 'capacity' => (int) $schedule->capacity, 'allocated_quantity' => $allocated, 'staff_assigned' => $staffAssigned],
                        'Upcoming tour readiness requires attention: ' . str_replace('_', ' ', implode(' and ', $issues)) . '.',
                        90
                    ));
                }
            });

        return $plans;
    }

    /**
     * Persist a plan and optionally enrich the summary via an external
     * LLM call (Anthropic API) when AI_API_KEY is configured.
     */
    protected function store(string $type, string $subject, array $input, array $recommendation, string $summary, float $confidence = 75.00): AiResourcePlan
    {
        if (config('services.ai.key')) {
            $summary = $this->enrichSummaryWithLLM($type, $subject, $recommendation, $summary);
        }

        return AiResourcePlan::create([
            'plan_type' => $type,
            'subject' => $subject,
            'input_snapshot' => $input,
            'recommendation' => $recommendation,
            'summary' => $summary,
            'confidence_score' => $confidence,
            'status' => 'generated',
        ]);
    }

    protected function enrichSummaryWithLLM(string $type, string $subject, array $recommendation, string $fallback): string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => config('services.ai.key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-6',
                'max_tokens' => 300,
                'messages' => [[
                    'role' => 'user',
                    'content' => "Summarize this {$type} recommendation for '{$subject}' in 2 short sentences for a travel agency manager: " . json_encode($recommendation),
                ]],
            ]);

            return $response->json('content.0.text') ?: $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }
}
