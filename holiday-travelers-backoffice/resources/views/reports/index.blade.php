@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Business intelligence</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Travel performance reports</h2><p class="mt-2 text-sm text-gray-500">Revenue, partner activity, refunds, and campaign ROI in one place.</p></div><a href="{{ route('reports.export') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">Export report</a></div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Revenue</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">₱{{ number_format($reportStats['revenue'], 2) }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Net margin</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ number_format($reportStats['netMargin'], 1) }}%</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Refunds</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">₱{{ number_format($reportStats['refunds'], 2) }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Partners paid</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">₱{{ number_format($reportStats['partnersPaid'], 2) }}</div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-border p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
            <div>
                <h2 class="font-heading font-semibold text-primary text-xl">Travel performance reports</h2>
                <p class="text-sm text-gray-500">Revenue, partner activity, refunds, and campaign ROI in one place.</p>
            </div>
            <a href="{{ route('reports.export') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium">Export report</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-border bg-gray-50 p-4">
                <h3 class="font-heading font-semibold text-primary mb-3">Monthly revenue</h3>
                <div class="h-52 flex items-end gap-3">
                    @php $maxRevenue = max($reportStats['monthlyRevenue']->max(), 1); @endphp
                    @foreach ($reportStats['monthlyRevenue'] as $amount)
                        <div class="flex-1 rounded-t-xl bg-gradient-to-t from-primary to-sky-400" style="height: {{ max(($amount / $maxRevenue) * 100, $amount > 0 ? 8 : 0) }}%; min-height:{{ $amount > 0 ? 30 : 0 }}px;"></div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-border bg-gray-50 p-4">
                <h3 class="font-heading font-semibold text-primary mb-3">Top performing channels</h3>
                <div class="space-y-4">
                    @forelse ($reportStats['channelConversions'] as $channel)
                        @php $share = $reportStats['totalConversions'] > 0 ? ($channel->conversions / $reportStats['totalConversions']) * 100 : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1"><span>{{ ucfirst(str_replace('_', ' ', $channel->channel)) }}</span><span>{{ number_format($share, 1) }}%</span></div>
                            <div class="h-2 rounded-full bg-gray-200"><div class="h-2 rounded-full bg-secondary" style="width:{{ $share }}%"></div></div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No campaign conversion data available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection