@extends('layouts.app')
@section('title', 'Revenue')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Financial operations</p>
            <h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Revenue</h2>
            <p class="mt-2 text-sm text-gray-500">Review received booking payments, refunds, and outstanding balances.</p>
        </div>
        <span class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">Live finance workspace</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Net collected</p>
            <p class="mt-2 font-heading text-2xl font-semibold tabular-nums text-primary">&#8369;{{ number_format($revenue, 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Cash received after refunds</p>
        </article>
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Refunds</p>
            <p class="mt-2 font-heading text-2xl font-semibold tabular-nums text-primary">&#8369;{{ number_format($refunds, 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Payments recorded as refunded</p>
        </article>
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Outstanding</p>
            <p class="mt-2 font-heading text-2xl font-semibold tabular-nums text-warning">&#8369;{{ number_format($outstanding, 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Remaining on confirmed and completed bookings</p>
        </article>
    </div>

    <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="border-b border-border p-5">
            <h2 class="font-heading text-xl font-semibold text-primary">Revenue transactions</h2>
            <p class="mt-1 text-sm text-gray-500">Payments linked to confirmed and completed bookings.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[42rem] text-left text-sm">
                <thead class="bg-background text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Booking</th>
                        <th class="px-5 py-3 font-semibold">Customer</th>
                        <th class="px-5 py-3 text-right font-semibold">Amount</th>
                        <th class="px-5 py-3 font-semibold">Method</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($payments as $payment)
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-3 font-medium text-primary">{{ $payment->booking?->reference_no ?? '—' }}</td>
                            <td class="px-5 py-3">{{ $payment->booking?->customer_name ?? '—' }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums text-primary">&#8369;{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td class="px-5 py-3 capitalize">{{ $payment->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No payment transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-border px-5 py-4">{{ $payments->links() }}</div>
    </section>
</div>
@endsection
