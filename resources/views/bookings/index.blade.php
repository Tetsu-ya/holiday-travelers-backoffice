@extends('layouts.app')
@section('title', 'Bookings')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Travel operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Bookings overview</h2><p class="mt-2 text-sm text-gray-500">Track customers, departure schedules, and payment status.</p></div><div class="flex flex-wrap gap-2"><a href="{{ route('bookings.create') }}" class="inline-flex items-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ New booking</a><a href="{{ route('reports.export') }}" class="inline-flex items-center rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-primary">Export</a></div></div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Total bookings</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['total'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Confirmed</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['confirmed'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Pending</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['pending'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Cancelled</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $bookingStats['cancelled'] }}</div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-border p-5 shadow-sm">
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Directory</p><h2 class="mt-1 font-heading font-semibold text-primary text-xl">All bookings</h2>
            </div>
            <div class="flex flex-wrap gap-2">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 border-b border-border bg-background/50 p-4 md:grid-cols-4">
            <select class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-gray-600">
                <option>All statuses</option>
                <option>Pending</option>
                <option>Confirmed</option>
                <option>Completed</option>
                <option>Cancelled</option>
            </select>
            <select class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-gray-600">
                <option>All destinations</option>
                <option>Bali</option>
                <option>Tokyo</option>
                <option>Dubai</option>
            </select>
            <select class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-gray-600">
                <option>All agents</option>
                <option>Aisha</option>
                <option>Marco</option>
                <option>Rina</option>
            </select>
            <input type="date" class="rounded-lg border border-border bg-white px-3 py-2 text-sm text-gray-600">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-background text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
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
                            <td class="py-4 pr-4 font-medium text-primary">{{ $booking->reference_no ?? 'HT-' . $booking->id }}</td>
                            <td class="py-3 pr-4">{{ $booking->customer_name }}</td>
                            <td class="py-3 pr-4">{{ $booking->tourPackage->name ?? '—' }}</td>
                            <td class="py-3 pr-4">{{ $booking->travel_date }}</td>
                            <td class="py-3 pr-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if (($booking->status ?? 'pending') === 'confirmed') bg-success/10 text-success
                                    @elseif (($booking->status ?? 'pending') === 'completed') bg-secondary/10 text-secondary
                                    @elseif (($booking->status ?? 'pending') === 'cancelled') bg-red-100 text-red-700
                                    @else bg-warning/10 text-warning @endif">
                                    {{ ucfirst($booking->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="py-3 pr-4">{{ ucfirst($booking->payment_status ?? 'unpaid') }}</td>
                            <td class="py-3 pr-4">₱{{ number_format($booking->total_amount ?? 0, 2) }}</td>
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