<?php

namespace App\Services;

use App\Models\AiResourcePlan;
use App\Models\Booking;
use App\Models\MarketingCampaign;
use App\Models\Supplier;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Http;

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
        $history = Booking::where('tour_package_id', $package->id)
            ->selectRaw("date_trunc('month', travel_date) as month, SUM(pax) as pax")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $avgPax = $history->avg('pax') ?? 0;
        $forecast = round($avgPax * 1.1); // naive seasonal growth placeholder

        return $this->store('demand_forecast', $package->name, [
            'history' => $history,
            'slots_available' => $package->slots,
        ], [
            'forecast_pax_next_month' => $forecast,
            'recommended_slots' => max($forecast, $package->slots),
        ], "Based on recent bookings, {$package->name} is projected to need approximately {$forecast} pax capacity next month.");
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

        return $this->store('supplier_allocation', $category, [
            'category' => $category,
            'location' => $location,
        ], [
            'ranked_candidates' => $candidates,
            'top_pick_id' => $best?->id,
        ], $best ? "{$best->name} is the top-ranked {$category} supplier by reliability and cost." : 'No active suppliers found for this category.');
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
            ->sortBy('cost_per_conversion');

        $topChannel = $campaigns->first();

        return $this->store('marketing_budget', 'all_channels', [
            'channel_performance' => $campaigns,
        ], [
            'ranked_channels' => $campaigns->values(),
            'recommended_focus_channel' => $topChannel?->channel,
        ], $topChannel ? "The '{$topChannel->channel}' channel has the lowest cost per conversion and is recommended for increased budget." : 'Insufficient campaign data to generate a recommendation.');
    }

    /**
     * Persist a plan and optionally enrich the summary via an external
     * LLM call (Anthropic API) when AI_API_KEY is configured.
     */
    protected function store(string $type, string $subject, array $input, array $recommendation, string $summary): AiResourcePlan
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
            'confidence_score' => 75.00,
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
