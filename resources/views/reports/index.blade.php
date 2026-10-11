@extends('layouts.app')

@section('title', 'Financial Reporting & Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Finance / Reporting</p>
            <h2 class="mt-1 font-heading text-3xl font-semibold tracking-tight text-primary">Financial Reporting &amp; Analytics</h2>
            <p class="mt-2 text-sm text-gray-500">A live summary of payment and booking records from your back office.</p>
        </div>
        <div class="flex flex-wrap items-end gap-2">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="report-from" class="mb-1 block text-xs font-medium text-gray-500">From</label>
                    <input id="report-from" type="date" name="from" value="{{ $from->toDateString() }}" required class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-primary">
                </div>
                <div>
                    <label for="report-to" class="mb-1 block text-xs font-medium text-gray-500">To</label>
                    <input id="report-to" type="date" name="to" value="{{ $to->toDateString() }}" required class="rounded-lg border border-border bg-card px-3 py-2 text-sm text-primary">
                </div>
                <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-secondary">Apply</button>
                <a href="{{ route('reports.financial-export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="rounded-lg border border-border bg-card px-4 py-2 text-sm font-semibold text-primary transition hover:border-secondary hover:text-secondary">Export CSV</a>
            </form>
            <a href="{{ route('reports.export') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-secondary hover:underline">Bookings CSV</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-error/20 bg-error/10 px-4 py-3 text-sm text-error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-border border-t-4 border-t-primary bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total revenue</p>
            <p class="mt-2 font-heading text-2xl font-semibold tabular-nums text-primary">&#8369;{{ number_format($reportStats['revenue'], 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Net cash received after &#8369;{{ number_format($reportStats['refunds'], 2) }} in refunds</p>
        </article>
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total bookings</p>
            <p class="mt-2 font-heading text-2xl font-semibold tabular-nums text-primary">{{ number_format($reportStats['bookings']) }}</p>
            <p class="mt-1 text-xs text-gray-400">Created during this period, all statuses</p>
        </article>
        <article class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p class="text-sm text-gray-500">Gross received</p>
            <p class="mt-2 font-heading text-2xl font-semibold tabular-nums text-primary">&#8369;{{ number_format($reportStats['grossRevenue'], 2) }}</p>
            <p class="mt-1 text-xs text-gray-400">Before &#8369;{{ number_format($reportStats['refunds'], 2) }} in refunds</p>
        </article>
    </div>

    <div class="rounded-xl border border-accent/20 bg-accent/5 px-4 py-3 text-sm leading-6 text-gray-600">
        Revenue uses payments recorded as paid or partial, grouped by payment date, less refunds. This report covers cash revenue and bookings only; it does not calculate operating profit or partner payouts.
    </div>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.6fr)_minmax(18rem,1fr)]">
        <section class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">Revenue overview</h3>
                    <p class="mt-1 text-sm text-gray-500">Monthly cash revenue after refunds, within the selected dates.</p>
                </div>
                <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">PHP</span>
            </div>
            @php
                $maxRevenue = max(1, collect($reportStats['monthlyRevenue'])->max('amount') ?? 0);
            @endphp
            <div class="mt-6 overflow-x-auto pb-1">
                <div class="flex h-56 min-w-max items-end gap-3 px-1 sm:min-w-0 sm:gap-2">
                    @foreach ($reportStats['monthlyRevenue'] as $month)
                        @php
                            $amount = $month['amount'];
                            $height = $amount > 0 ? max(8, ($amount / $maxRevenue) * 100) : 3;
                        @endphp
                        <div class="flex h-full min-w-10 flex-1 flex-col items-center justify-end gap-2 sm:min-w-0" title="{{ $month['label'] }}: &#8369;{{ number_format($amount, 2) }}">
                            <span class="whitespace-nowrap text-[10px] font-medium tabular-nums text-gray-500">{{ $amount > 0 ? number_format($amount / 1000, 1) . 'k' : '' }}</span>
                            <div class="flex h-36 w-full items-end rounded-t-md bg-background">
                                <div class="w-full rounded-t-md {{ $amount < 0 ? 'bg-error' : 'bg-gradient-to-t from-primary to-sky-400' }}" style="height: {{ $height }}%"></div>
                            </div>
                            <span class="text-[10px] font-medium text-gray-500">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @if (collect($reportStats['monthlyRevenue'])->every(fn ($month) => $month['amount'] === 0.0))
                <p class="mt-3 text-center text-xs text-gray-400">No received payments in the selected period.</p>
            @endif
        </section>

        <section class="rounded-2xl border border-border bg-card p-5 shadow-sm">
            <div>
                <h3 class="font-heading text-lg font-semibold text-primary">Payment methods</h3>
                <p class="mt-1 text-sm text-gray-500">Booking payment records by method, including every status.</p>
            </div>
            @if (count($reportStats['paymentMethods']))
                <div class="mt-5 flex flex-col items-center gap-5 sm:flex-row xl:flex-col 2xl:flex-row">
                    <div class="relative h-40 w-40 shrink-0 rounded-full" role="img" aria-label="Payment method distribution" style="background: {{ $reportStats['paymentMethodGradient'] }}">
                        <div class="absolute inset-7 flex flex-col items-center justify-center rounded-full bg-card text-center">
                            <span class="text-xl font-bold tabular-nums text-primary">&#8369;{{ number_format($reportStats['paymentRecordTotal'], 0) }}</span>
                            <span class="text-[10px] text-gray-500">recorded</span>
                        </div>
                    </div>
                    <div class="w-full space-y-3">
                        @foreach ($reportStats['paymentMethods'] as $method)
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="flex min-w-0 items-center gap-2 text-gray-600">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $method['color'] }}"></span>
                                <span class="truncate">{{ $method['name'] }}</span>
                            </span>
                            <span class="shrink-0 text-right font-medium tabular-nums text-primary">{{ number_format($method['percentage'], 1) }}% <span class="text-xs font-normal text-gray-500">(&#8369;{{ number_format($method['amount'], 2) }})</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <p class="mt-4 border-t border-border pt-3 text-xs leading-5 text-gray-500">The chart totals recorded payment amounts, not cash received. Paid, partial, pending, cancelled, and refunded records are included; only paid and partial payments count toward revenue, while refunds are deducted separately.</p>
            @else
                <div class="mt-5 flex items-start gap-4 rounded-xl border border-dashed border-border bg-background p-4 sm:p-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5h18M5.25 4.5h13.5A2.25 2.25 0 0 1 21 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 17.25V6.75A2.25 2.25 0 0 1 5.25 4.5ZM7.5 15h3" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-primary">No payment records in this date range</p>
                        <p class="mt-1 text-sm leading-6 text-gray-500">Payment methods include paid, partial, pending, cancelled, and refunded records, dated by payment date or record creation date when no payment date exists. Try widening the date range or check Revenue for received payments.</p>
                        <a href="{{ route('revenue.index') }}" class="mt-3 inline-flex items-center text-sm font-semibold text-secondary hover:underline">View revenue transactions <span class="ml-1" aria-hidden="true">&rarr;</span></a>
                    </div>
                </div>
            @endif
            <div class="mt-5 border-t border-border pt-4">
                <h4 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Payment status breakdown</h4>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 xl:grid-cols-2 2xl:grid-cols-3">
                    @foreach ($reportStats['paymentStatusSummary'] as $status)
                        <div class="rounded-lg bg-background px-3 py-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-500">{{ $status['name'] }}</span>
                                <span class="text-xs font-semibold tabular-nums text-primary">{{ number_format($status['count']) }}</span>
                            </div>
                            <p class="mt-1 text-sm font-semibold tabular-nums text-primary">&#8369;{{ number_format($status['amount'], 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-border px-5 py-4">
            <div>
                <h3 class="font-heading text-lg font-semibold text-primary">Recent transactions</h3>
                <p class="mt-1 text-sm text-gray-500">All payment statuses; uses record date when payment date is unavailable.</p>
            </div>
            <span class="text-xs text-gray-400">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[42rem] text-left text-sm">
                <thead class="bg-background text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Date</th>
                        <th class="px-5 py-3 font-semibold">Description</th>
                        <th class="px-5 py-3 font-semibold">Category</th>
                        <th class="px-5 py-3 text-right font-semibold">Amount</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($reportStats['transactions'] as $transaction)
                        <tr class="transition hover:bg-background/70">
                            <td class="whitespace-nowrap px-5 py-3 text-gray-500">{{ $transaction['date']?->format('d M Y') ?? '—' }}</td>
                            <td class="max-w-xs truncate px-5 py-3 font-medium text-primary">{{ $transaction['description'] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $transaction['category'] }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-right font-semibold tabular-nums {{ $transaction['status'] === 'refunded' ? 'text-error' : (in_array($transaction['status'], ['pending', 'cancelled'], true) ? 'text-gray-500' : 'text-success') }}">
                                {{ $transaction['status'] === 'refunded' ? '−' : (in_array($transaction['status'], ['pending', 'cancelled'], true) ? '' : '+') }}&#8369;{{ number_format(abs($transaction['amount']), 2) }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $transaction['status'] === 'refunded' ? 'bg-error/10 text-error' : (in_array($transaction['status'], ['pending', 'cancelled'], true) ? 'bg-gray-100 text-gray-600' : 'bg-success/10 text-success') }}">{{ $transaction['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No payment transactions found in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
