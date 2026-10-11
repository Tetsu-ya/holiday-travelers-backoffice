@extends('layouts.app')
@section('title', 'AI Resource Planning')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Decision support</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">AI resource planning</h2><p class="mt-2 text-sm text-gray-500">Turn tour schedules, weather forecasts, traffic, supplier, and campaign signals into practical next steps.</p></div><span class="flex items-center gap-2 rounded-full bg-accent/10 px-3 py-1.5 text-xs font-semibold text-accent"><span class="h-1.5 w-1.5 rounded-full bg-accent"></span>Live planning engine</span></div>
    <div class="rounded-2xl border border-border border-t-4 border-t-accent bg-card p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-accent">Travel readiness</p><h2 class="mt-1 font-heading text-xl font-semibold text-primary">Check a scheduled tour</h2><p class="mt-1 max-w-2xl text-sm text-gray-500">Get a clear travel outlook based on the tour's weather and road-traffic conditions. This check does not verify traveler documents, visas, insurance, payments, or bookings.</p></div><form method="POST" action="{{ route('ai-planning.travel-readiness') }}" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">@csrf<select name="schedule_id" required class="min-w-0 rounded-lg border border-border bg-card px-3 py-2.5 text-sm text-primary sm:min-w-[320px]"><option value="">Select a scheduled tour</option>@foreach($schedules as $schedule)<option value="{{ $schedule->id }}">{{ $schedule->tourPackage->name }} · {{ $schedule->schedule_date->format('d M Y') }} · {{ $schedule->location ?: $schedule->tourPackage->destination }}</option>@endforeach</select><button class="rounded-lg bg-accent px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90">Predict readiness</button></form></div>
        @if(session('travel_readiness')) @php($readiness = session('travel_readiness')) @include('ai-planning._travel-readiness', ['readiness' => $readiness]) @endif
    </div>
</div>
@endsection
