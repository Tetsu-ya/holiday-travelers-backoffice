@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Operations overview</p>
            <h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary sm:text-3xl">A clear view of today’s travel business.</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-500">Monitor bookings, partners, suppliers, and campaigns from one working view.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:opacity-90">
            Open bookings
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        @php
            $cards = [
                ['Total Bookings', $kpis['total_bookings'], 'bg-primary'],
                ['Revenue (This Month)', '₱' . number_format($kpis['revenue_this_month'], 2), 'bg-secondary'],
                ['Active Partners', $kpis['active_partners'], 'bg-accent'],
                ['Active Suppliers', $kpis['active_suppliers'], 'bg-success'],
                ['Running Campaigns', $kpis['running_campaigns'], 'bg-warning'],
            ];
        @endphp

        @foreach ($cards as [$label, $value, $color])
            <div class="group relative overflow-hidden rounded-2xl border border-border bg-card p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="absolute inset-x-0 top-0 h-1 {{ $color }}"></div>
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">{{ $label }}</span>
                    <span class="inline-block h-2.5 w-2.5 rounded-full {{ $color }} shadow-[0_0_0_4px_rgba(111,169,230,0.12)]"></span>
                </div>
                <p class="font-heading text-3xl font-semibold tracking-tight text-primary">{{ $value }}</p>
                <p class="mt-2 text-xs text-gray-400">Compared with the current workspace</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm xl:col-span-2">
            <div class="flex items-start justify-between border-b border-border px-5 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Momentum</p>
                    <h2 class="mt-1 font-heading font-semibold text-primary">Bookings trend</h2>
                </div>
                <span class="rounded-full bg-secondary/10 px-3 py-1.5 text-xs font-semibold text-secondary">This year</span>
            </div>

            <div class="relative mx-5 mt-5 flex h-64 items-end gap-3 border-b border-border bg-[linear-gradient(to_bottom,transparent_24.5%,rgba(148,163,184,0.12)_25%,transparent_25.5%,transparent_49.5%,rgba(148,163,184,0.12)_50%,transparent_50.5%,transparent_74.5%,rgba(148,163,184,0.12)_75%,transparent_75.5%)] pb-3">
                @php $bars = [38, 52, 60, 66, 78, 88, 94]; @endphp
                @foreach ($bars as $i => $height)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full max-w-8 rounded-t-xl shadow-lg shadow-primary/10 transition hover:scale-x-110 {{ $i % 2 === 0 ? 'bg-gradient-to-t from-primary to-sky-400' : 'bg-gradient-to-t from-orange-400 to-orange-300' }}" style="height: {{ $height }}%; min-height:34px;"></div>
                        <span class="mt-2 text-[11px] text-gray-400">{{ ['Jan','Feb','Mar','Apr','May','Jun','Jul'][$i] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3 p-5">
                <div class="rounded-xl border border-border bg-background p-4">
                    <div class="mb-2 text-xs text-gray-500">Top destination</div>
                    <div class="font-heading text-2xl font-semibold text-primary">Bali</div>
                </div>
                <div class="rounded-xl border border-border bg-background p-4">
                    <div class="mb-2 text-xs text-gray-500">Best channel</div>
                    <div class="font-heading text-2xl font-semibold text-primary">Referral</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Decision support</p>
                    <h2 class="mt-1 font-heading font-semibold text-primary">AI insights</h2>
                </div>
                <span class="flex items-center gap-2 rounded-full bg-accent/10 px-3 py-1.5 text-xs font-semibold text-accent"><span class="h-1.5 w-1.5 rounded-full bg-accent"></span>Live</span>
            </div>

            <div class="space-y-3">
                <div class="rounded-xl border border-secondary/20 border-l-4 bg-secondary/5 p-3.5">
                    <p class="font-medium text-primary">Bali demand is rising</p>
                    <p class="text-sm text-gray-500 mt-1">Next month demand is forecast 18% above baseline.</p>
                </div>
                <div class="rounded-xl border border-accent/20 border-l-4 bg-accent/5 p-3.5">
                    <p class="font-medium text-primary">Supplier match</p>
                    <p class="text-sm text-gray-500 mt-1">Blue Horizon Hotels gives the best reliability-to-cost ratio.</p>
                </div>
                <div class="rounded-xl border border-success/20 border-l-4 bg-success/5 p-3.5">
                    <p class="font-medium text-primary">Marketing budget</p>
                    <p class="text-sm text-gray-500 mt-1">Referral spend has the strongest conversion rate in the portfolio.</p>
                </div>
            </div>

            <a href="{{ route('ai-planning.index') }}" class="mt-5 inline-flex items-center rounded-xl border border-secondary/30 bg-secondary/10 px-4 py-2.5 text-sm font-semibold text-secondary transition hover:bg-secondary hover:text-white">
                View AI Planning
            </a>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-border shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-heading font-semibold text-primary">Recent bookings</h2>
            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">Updated 5m ago</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500">
                    <tr>
                        <th class="pb-3 pr-4">Reference</th>
                        <th class="pb-3 pr-4">Customer</th>
                        <th class="pb-3 pr-4">Package</th>
                        <th class="pb-3 pr-4">Travel date</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3 pr-4">Payment</th>
                        <th class="pb-3 pr-4">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($recentBookings as $booking)
                        <tr>
                            <td class="py-3 pr-4 font-medium text-primary">{{ $booking->reference_no }}</td>
                            <td class="py-3 pr-4">{{ $booking->customer_name }}</td>
                            <td class="py-3 pr-4">{{ $booking->tourPackage?->name ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $booking->travel_date?->format('d M Y') }}</td>
                            <td class="py-3 pr-4">{{ ucfirst($booking->status) }}</td>
                            <td class="py-3 pr-4">{{ ucfirst($booking->payment_status) }}</td>
                            <td class="py-3 pr-4">₱{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
