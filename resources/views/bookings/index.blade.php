@extends('layouts.app')
@section('title', 'Bookings')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Travel operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Bookings overview</h2><p class="mt-2 text-sm text-gray-500">Track customers, departure schedules, and payment status.</p></div><div class="flex flex-wrap gap-2"><a href="{{ route('bookings.create') }}" class="inline-flex items-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ New booking</a><a href="{{ route('reports.export') }}" class="inline-flex items-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-primary">Export</a></div></div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="group rounded-2xl border border-border border-t-4 border-t-primary bg-card p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between"><div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Total bookings</div><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary/10 text-primary">#</span></div>
            <div class="mt-3 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['total'] }}</div>
        </div>
        <div class="group rounded-2xl border border-border border-t-4 border-t-success bg-card p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between"><div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Confirmed</div><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-success/10 text-success">✓</span></div>
            <div class="mt-3 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['confirmed'] }}</div>
        </div>
        <div class="group rounded-2xl border border-border border-t-4 border-t-warning bg-card p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between"><div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Pending</div><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-warning/10 text-warning">…</span></div>
            <div class="mt-3 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['pending'] }}</div>
        </div>
        <div class="group rounded-2xl border border-border border-t-4 border-t-error bg-card p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center justify-between"><div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Cancelled</div><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-error/10 text-error">×</span></div>
            <div class="mt-3 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['cancelled'] }}</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Directory</p><h2 class="mt-1 font-heading font-semibold text-primary text-xl">All bookings</h2>
            </div>
        </div>

        @if(!empty($filters['travel_month']) || !empty($filters['paid_month']))
            <div class="flex flex-col gap-3 border-b border-border bg-accent/5 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-600">
                    @if(!empty($filters['paid_month']))
                        Showing bookings with payments received in
                        <span class="font-semibold text-primary">{{ date('F Y', mktime(0, 0, 0, (int) $filters['paid_month'], 1, (int) ($filters['paid_year'] ?? now()->year))) }}</span>
                    @else
                        Showing bookings travelling in
                        <span class="font-semibold text-primary">{{ date('F Y', mktime(0, 0, 0, (int) $filters['travel_month'], 1, (int) ($filters['travel_year'] ?? now()->year))) }}</span>
                    @endif
                    @if(!empty($filters['status']))
                        with status <span class="font-semibold capitalize text-primary">{{ $filters['status'] }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500">Booked revenue&nbsp;<span class="font-semibold text-primary">&#8369;{{ number_format((float) $bookings->sum('total_amount'), 2) }}</span></span>
                    <a href="{{ route('bookings.index') }}" class="rounded-lg border border-border bg-white px-3 py-1.5 text-xs font-semibold text-primary transition hover:border-secondary hover:text-secondary">Clear filter</a>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-y border-border bg-primary/[0.04] text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <tr>
                        <th class="pb-3 pr-4">Reference</th>
                        <th class="pb-3 pr-4">Customer</th>
                        <th class="pb-3 pr-4">Package</th>
                        <th class="pb-3 pr-4">Travel date</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3 pr-4">Payment</th>
                        <th class="pb-3 pr-4">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($bookings as $booking)
                        <tr class="transition hover:bg-background/70">
                            <td class="py-4 pr-4"><span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-bold tracking-wide text-primary">{{ $booking->reference_no ?? 'HT-' . $booking->id }}</span></td>
                            <td class="py-3 pr-4"><div class="font-medium text-primary">{{ $booking->customer_name }}</div><div class="mt-0.5 text-xs text-gray-400">{{ $booking->customer_email ?: 'No email provided' }}</div></td>
                            <td class="py-3 pr-4"><span class="inline-flex items-center gap-2 text-gray-700"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-secondary/10 text-xs text-secondary">✦</span>{{ $booking->tourPackage->name ?? 'Package not assigned' }}</span></td>
                            <td class="py-3 pr-4"><span class="inline-flex items-center gap-2 rounded-lg bg-background px-2.5 py-1.5 font-medium text-primary"><span class="text-xs text-accent">▣</span>{{ $booking->travel_date?->format('d M Y') }}@if ($booking->travel_time)<span class="text-xs text-gray-500">{{ \Illuminate\Support\Carbon::parse($booking->travel_time)->format('g:i A') }}</span>@endif</span></td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                    @if (($booking->status ?? 'pending') === 'confirmed') bg-success/10 text-success
                                    @elseif (($booking->status ?? 'pending') === 'completed') bg-secondary/10 text-secondary
                                    @elseif (($booking->status ?? 'pending') === 'cancelled') bg-error/10 text-error
                                    @elseif (($booking->status ?? 'pending') === 'refunded') bg-accent/10 text-accent
                                    @else bg-warning/10 text-warning @endif">
                                    {{ ucfirst($booking->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="py-3 pr-4"><span class="inline-flex rounded-full {{ ($booking->payment_status ?? 'unpaid') === 'paid' ? 'bg-success/10 text-success' : (($booking->payment_status ?? 'unpaid') === 'partial' ? 'bg-warning/10 text-warning' : (($booking->payment_status ?? 'unpaid') === 'refunded' ? 'bg-error/10 text-error' : (($booking->payment_status ?? 'unpaid') === 'cancelled' ? 'bg-error/10 text-error' : 'bg-gray-100 text-gray-600'))) }} px-2.5 py-1 text-xs font-semibold capitalize">{{ ucfirst($booking->payment_status ?? 'unpaid') }}</span></td>
                            <td class="py-3 pr-4"><span class="font-heading text-base font-semibold text-primary">₱{{ number_format($booking->total_amount ?? 0, 2) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-400">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border px-5 py-4">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
