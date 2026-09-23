@extends('layouts.app')
@section('title', $campaign->name)

@section('content')
<div class="max-w-4xl space-y-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Marketing campaign</p>
            <h1 class="text-2xl font-semibold">{{ $campaign->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('campaigns.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-semibold text-primary shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary hover:shadow-md active:translate-y-0 active:scale-95 dark:bg-gray-800 dark:text-gray-100"><span class="transition-transform duration-200 group-hover:-translate-x-1">←</span><span>Back</span></a>
        </div>
    </div>

    <div class="bg-card rounded-xl border border-border shadow-sm p-6">
        <dl class="grid gap-5 md:grid-cols-2">
            <div><dt class="text-sm text-gray-500">Channel</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $campaign->channel) }}</dd></div>
            <div><dt class="text-sm text-gray-500">Status</dt><dd class="capitalize">{{ $campaign->status }}</dd></div>
            <div><dt class="text-sm text-gray-500">Budget</dt><dd>PHP {{ number_format((float) ($campaign->budget ?? 0), 2) }}</dd></div>
            <div><dt class="text-sm text-gray-500">Actual spend</dt><dd>PHP {{ number_format((float) ($campaign->actual_spend ?? 0), 2) }}</dd></div>
            <div><dt class="text-sm text-gray-500">Leads generated</dt><dd>{{ $campaign->leads_generated ?? 0 }}</dd></div>
            <div><dt class="text-sm text-gray-500">Conversions</dt><dd>{{ $campaign->conversions ?? 0 }}</dd></div>
            <div><dt class="text-sm text-gray-500">Start date</dt><dd>{{ $campaign->start_date }}</dd></div>
            <div><dt class="text-sm text-gray-500">End date</dt><dd>{{ $campaign->end_date }}</dd></div>
        </dl>
    </div>
</div>
@endsection
