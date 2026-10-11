@php
    $level = $readiness['recommendation']['level'] ?? 'setup';
    $outcome = match ($level) {
        'safe' => ['label' => 'READY TO TRAVEL', 'panel' => 'border-success/30 bg-success/5', 'icon_class' => 'bg-success/10 text-success', 'text_class' => 'text-success', 'icon' => '✓'],
        'caution' => ['label' => 'TRAVEL WITH CAUTION', 'panel' => 'border-warning/30 bg-warning/5', 'icon_class' => 'bg-warning/10 text-warning', 'text_class' => 'text-warning', 'icon' => '!'],
        'review' => ['label' => 'NOT READY TO TRAVEL', 'panel' => 'border-error/30 bg-error/5', 'icon_class' => 'bg-error/10 text-error', 'text_class' => 'text-error', 'icon' => '!'],
        default => ['label' => 'UNABLE TO CONFIRM', 'panel' => 'border-warning/30 bg-warning/5', 'icon_class' => 'bg-warning/10 text-warning', 'text_class' => 'text-warning', 'icon' => '?'],
    };
    $weatherStatus = $readiness['weather']['status'] ?? '';
    $trafficStatus = $readiness['traffic']['status'] ?? '';
@endphp

<div class="mt-5 space-y-4 border-t border-border pt-5">
    <section class="rounded-2xl border p-5 sm:p-6 {{ $outcome['panel'] }}" aria-live="polite">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full text-2xl font-bold {{ $outcome['icon_class'] }}" aria-hidden="true">{{ $outcome['icon'] }}</span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Tour travel outlook</p>
                    <h3 class="mt-1 text-xl font-bold tracking-tight {{ $outcome['text_class'] }}">{{ $outcome['label'] }}</h3>
                    <p class="mt-2 max-w-2xl text-sm text-gray-500">{{ $readiness['recommendation']['summary'] }}</p>
                </div>
            </div>
            <div class="shrink-0 rounded-xl border border-border bg-card px-5 py-3 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Readiness score</p>
                <p class="mt-1 text-2xl font-bold text-primary">
                    @if($readiness['overall'] !== null)
                        {{ $readiness['overall'] }}<span class="text-sm font-medium text-gray-400"> / 100</span>
                    @else
                        <span class="text-base">Not available</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 border-t border-border/70 pt-4 text-sm text-gray-500">
            <span class="font-semibold text-primary">{{ $readiness['package'] }}</span>
            <span>{{ $readiness['destination'] }}</span>
            <span>{{ $readiness['date_display'] }}</span>
        </div>
    </section>

    <section class="rounded-2xl border border-border bg-background p-5 sm:p-6">
        <div>
            <h3 class="text-base font-semibold text-primary">Readiness checks</h3>
            <p class="mt-1 text-sm text-gray-500">Conditions assessed for this scheduled tour.</p>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            <article class="rounded-xl border border-border bg-card p-4">
                <div class="flex items-center justify-between gap-3">
                    <h4 class="font-semibold text-primary">Weather</h4>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $weatherStatus === 'available' ? 'bg-success/10 text-success' : (in_array($weatherStatus, ['estimated'], true) ? 'bg-warning/10 text-warning' : 'bg-error/10 text-error') }}">
                        {{ $weatherStatus === 'available' ? 'Forecast available' : ($weatherStatus === 'estimated' ? 'Estimated' : 'Unavailable') }}
                    </span>
                </div>
                <p class="mt-3 text-sm text-gray-500">{{ $readiness['weather']['summary'] }}</p>
                <p class="mt-2 text-xs text-gray-400">
                    @if($readiness['weather_score'] !== null)
                        Score: {{ $readiness['weather_score'] }}/100
                        @if(isset($readiness['weather']['low'], $readiness['weather']['high']))
                            · {{ $readiness['weather']['low'] }}°–{{ $readiness['weather']['high'] }}°
                        @endif
                    @else
                        Not scored
                    @endif
                </p>
                @if(isset($readiness['weather']['location']))
                    <p class="mt-1 text-xs text-gray-400">Forecast location: {{ $readiness['weather']['location'] }}</p>
                @endif
            </article>

            <article class="rounded-xl border border-border bg-card p-4">
                <div class="flex items-center justify-between gap-3">
                    <h4 class="font-semibold text-primary">Road traffic</h4>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $trafficStatus === 'available' ? 'bg-success/10 text-success' : (in_array($trafficStatus, ['estimated'], true) ? 'bg-warning/10 text-warning' : 'bg-error/10 text-error') }}">
                        {{ $trafficStatus === 'available' ? 'Route available' : ($trafficStatus === 'estimated' ? 'Estimated' : 'Unavailable') }}
                    </span>
                </div>
                <p class="mt-3 text-sm text-gray-500">{{ $readiness['traffic']['summary'] }}</p>
                <p class="mt-2 text-xs text-gray-400">
                    @if($readiness['traffic_score'] !== null)
                        Score: {{ $readiness['traffic_score'] }}/100
                        @if(isset($readiness['traffic']['duration_minutes']))
                            · {{ $readiness['traffic']['duration_minutes'] }} min
                        @endif
                    @else
                        Not scored
                    @endif
                </p>
                @if(isset($readiness['traffic']['departure_at']))
                    <p class="mt-1 text-xs text-gray-400">Estimate for {{ \Illuminate\Support\Carbon::parse($readiness['traffic']['departure_at'])->format('d M Y, g:i A') }}; road traffic only.</p>
                @endif
                @if(($readiness['traffic']['origin_precision'] ?? null) === 'city' || ($readiness['traffic']['destination_precision'] ?? null) === 'city')
                    <p class="mt-2 text-xs text-warning">A route point resolved only to city level. Add a street address or landmark for a more precise estimate.</p>
                @endif
            </article>
        </div>

        <p class="mt-4 rounded-lg bg-background px-3 py-2.5 text-xs leading-relaxed text-gray-400">
            This readiness result is based on weather and road-traffic conditions only. It does not verify traveler documents, visas, insurance, payments, or bookings.
            @if(isset($readiness['checked_at']))
                Checked {{ \Illuminate\Support\Carbon::parse($readiness['checked_at'])->format('d M Y, g:i A') }}.
            @endif
        </p>
    </section>
</div>
