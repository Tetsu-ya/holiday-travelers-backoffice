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
        <a href="{{ route('bookings.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:opacity-90">Open bookings</a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        @php
            $cards = [
                ['Total Bookings', $kpis['total_bookings'], 'bg-primary'],
                ['Revenue (This Month)', '₱' . number_format($kpis['revenue_this_month'], 2), 'bg-secondary'],
                ['Booked Value', '₱' . number_format($kpis['booked_value'], 2), 'bg-accent'],
                ['Outstanding', '₱' . number_format($kpis['outstanding'], 2), 'bg-success'],
                ['Running Campaigns', $kpis['running_campaigns'], 'bg-warning'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $color])
            <div class="group relative overflow-hidden rounded-2xl border border-border bg-card p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"><div class="absolute inset-x-0 top-0 h-1 {{ $color }}"></div><div class="mb-4 flex items-center justify-between"><span class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">{{ $label }}</span><span class="inline-block h-2.5 w-2.5 rounded-full {{ $color }} shadow-[0_0_0_4px_rgba(111,169,230,0.12)]"></span></div><p class="font-heading text-3xl font-semibold tracking-tight text-primary">{{ $value }}</p><p class="mt-2 text-xs text-gray-400">{{ $kpis['active_partners'] }} partners · {{ $kpis['active_suppliers'] }} suppliers</p></div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm xl:col-span-2">
            <div class="flex items-start justify-between border-b border-border px-5 py-5"><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Momentum</p><h2 class="mt-1 font-heading font-semibold text-primary">Bookings trend</h2></div><span class="rounded-full bg-secondary/10 px-3 py-1.5 text-xs font-semibold text-secondary">This year</span></div>
            <div class="relative mx-5 mt-5 flex h-64 items-end gap-3 border-b border-border bg-[linear-gradient(to_bottom,transparent_24.5%,rgba(148,163,184,0.12)_25%,transparent_25.5%,transparent_49.5%,rgba(148,163,184,0.12)_50%,transparent_50.5%,transparent_74.5%,rgba(148,163,184,0.12)_75%,transparent_75.5%)] pb-3">
                @php
                    $maxBookings = max($monthlyBookings->max(), 1);
                    $monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                @endphp
                @foreach ($monthlyBookings as $m => $count)<div class="flex flex-1 flex-col items-center" title="{{ $monthLabels[$m - 1] }}: {{ $count }} booking(s)"><div class="w-full max-w-8 rounded-t-xl shadow-lg shadow-primary/10 transition hover:scale-x-110 {{ ($m - 1) % 2 === 0 ? 'bg-gradient-to-t from-primary to-sky-400' : 'bg-gradient-to-t from-orange-400 to-orange-300' }}" style="height: {{ $count > 0 ? max(($count / $maxBookings) * 100, 8) : 0 }}%; min-height: {{ $count > 0 ? 8 : 2 }}px;"></div><span class="mt-2 text-[11px] text-gray-400">{{ $monthLabels[$m - 1] }}</span></div>@endforeach
            </div>
            <div class="grid grid-cols-2 gap-3 p-5"><div class="rounded-xl border border-border bg-background p-4"><div class="mb-2 text-xs text-gray-500">Top destination</div><div class="font-heading text-2xl font-semibold text-primary">{{ $topDestination->destination ?? '—' }}</div><div class="mt-1 text-xs text-gray-400">{{ $topDestination ? $topDestination->total . ' booking(s)' : 'No bookings yet' }}</div></div><div class="rounded-xl border border-border bg-background p-4"><div class="mb-2 text-xs text-gray-500">Best channel</div><div class="font-heading text-2xl font-semibold capitalize text-primary">{{ $bestChannel ? str_replace('_', ' ', $bestChannel->channel) : '—' }}</div><div class="mt-1 text-xs text-gray-400">{{ $bestChannel ? number_format((int) $bestChannel->total) . ' conversion(s)' : 'No campaigns yet' }}</div></div></div>
        </div>
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><div class="mb-5 flex items-center justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Decision support</p><h2 class="mt-1 font-heading font-semibold text-primary">Business snapshot</h2></div><span class="flex items-center gap-2 rounded-full bg-accent/10 px-3 py-1.5 text-xs font-semibold text-accent"><span class="h-1.5 w-1.5 rounded-full bg-accent"></span>Live</span></div><div class="space-y-3"><div class="rounded-xl border border-secondary/20 border-l-4 bg-secondary/5 p-3.5"><p class="font-medium text-primary">Top destination</p><p class="mt-1 text-sm text-gray-500">{{ $topDestination->destination ?? 'No bookings recorded yet' }}@if($topDestination) leads with {{ $topDestination->total }} booking(s).@endif</p></div><div class="rounded-xl border border-accent/20 border-l-4 bg-accent/5 p-3.5"><p class="font-medium text-primary">Best channel</p><p class="mt-1 text-sm text-gray-500">{{ $bestChannel ? ucfirst(str_replace('_', ' ', $bestChannel->channel)) . ' drives the most conversions (' . number_format((int) $bestChannel->total) . ').' : 'No campaign conversions recorded yet.' }}</p></div><div class="rounded-xl border border-success/20 border-l-4 bg-success/5 p-3.5"><p class="font-medium text-primary">Outstanding balance</p><p class="mt-1 text-sm text-gray-500">₱{{ number_format($kpis['outstanding'], 2) }} still owed across {{ $kpis['active_partners'] }} active partner(s).</p></div></div><a href="{{ route('ai-planning.index') }}" class="mt-5 inline-flex items-center rounded-xl border border-secondary/30 bg-secondary/10 px-4 py-2.5 text-sm font-semibold text-secondary transition hover:bg-secondary hover:text-white">View AI Planning</a></div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-col gap-2 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Latest activity</p>
                <h2 class="mt-1 font-heading font-semibold text-primary">Recent bookings</h2>
            </div>
            <span class="inline-flex items-center gap-2 self-start rounded-full bg-background px-3 py-1.5 text-xs font-semibold text-gray-500 sm:self-auto"><span class="h-1.5 w-1.5 rounded-full bg-success"></span>Updated {{ now()->format('d M Y, g:i A') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">Reference</th>
                        <th class="px-5 py-3.5">Customer</th>
                        <th class="px-5 py-3.5">Package</th>
                        <th class="px-5 py-3.5">Travel date</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Payment</th>
                        <th class="px-5 py-3.5 text-right">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($recentBookings as $booking)
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-4"><span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-bold tracking-wide text-primary">{{ $booking->reference_no ?? 'HT-' . $booking->id }}</span></td>
                            <td class="px-5 py-4"><div class="font-semibold text-primary">{{ $booking->customer_name }}</div><div class="mt-0.5 text-xs text-gray-400">{{ $booking->customer_email ?: 'No email provided' }}</div></td>
                            <td class="px-5 py-4 text-gray-600">{{ $booking->tourPackage?->name ?? 'Package not assigned' }}</td>
                            <td class="px-5 py-4"><span class="inline-flex items-center gap-2 rounded-lg bg-background px-2.5 py-1.5 font-medium text-primary"><span class="text-xs text-accent">▣</span>{{ $booking->travel_date?->format('d M Y') ?? '—' }}</span></td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold @if (($booking->status ?? 'pending') === 'confirmed') bg-success/10 text-success @elseif (($booking->status ?? 'pending') === 'completed') bg-secondary/10 text-secondary @elseif (($booking->status ?? 'pending') === 'cancelled') bg-error/10 text-error @elseif (($booking->status ?? 'pending') === 'refunded') bg-accent/10 text-accent @else bg-warning/10 text-warning @endif">{{ ucfirst($booking->status ?? 'pending') }}</span></td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ ($booking->payment_status ?? 'unpaid') === 'paid' ? 'bg-success/10 text-success' : (($booking->payment_status ?? 'unpaid') === 'partial' ? 'bg-warning/10 text-warning' : (($booking->payment_status ?? 'unpaid') === 'refunded' ? 'bg-error/10 text-error' : (($booking->payment_status ?? 'unpaid') === 'cancelled' ? 'bg-error/10 text-error' : 'bg-gray-100 text-gray-600'))) }}">{{ ucfirst($booking->payment_status ?? 'unpaid') }}</span></td>
                            <td class="px-5 py-4 text-right"><span class="font-heading font-semibold text-primary">₱{{ number_format($booking->total_amount ?? 0, 2) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
