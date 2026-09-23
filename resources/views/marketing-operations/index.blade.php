@extends('layouts.app')
@section('title', match ($module) {
    'promotions' => 'Promotions',
    'discount-codes' => 'Discount Codes',
    'calendar' => 'Marketing Calendar',
    default => 'Campaign Analytics',
})
@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Marketing operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">{{ match ($module) { 'promotions' => 'Promotions', 'discount-codes' => 'Discount Codes', 'calendar' => 'Marketing Calendar', default => 'Campaign Analytics' } }}</h2><p class="mt-2 text-sm text-gray-500">Plan offers, coordinate campaigns, and measure channel performance.</p></div><span class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">Live marketing workspace</span></div>
    @if ($module === 'promotions' || $module === 'discount-codes')
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
            <div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-5 py-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Quick entry</p><h3 class="mt-1 font-heading font-semibold text-primary">{{ $module === 'promotions' ? 'Create promotion' : 'Create discount code' }}</h3><p class="mt-1 text-sm text-gray-500">Manage {{ $module === 'promotions' ? 'campaign offers' : 'customer discount codes' }}.</p></div>
            <form method="POST" action="{{ route($module . '.store') }}" class="grid gap-4 p-5 md:grid-cols-3">
                @csrf
                @if ($module === 'promotions')
                    <input name="name" required placeholder="Promotion name" class="rounded-lg border border-border px-3 py-2">
                    <input name="description" placeholder="Description" class="rounded-lg border border-border px-3 py-2">
                @else
                    <input name="code" required placeholder="Code e.g. SUMMER20" class="rounded-lg border border-border px-3 py-2 uppercase">
                    <input name="description" placeholder="Description" class="rounded-lg border border-border px-3 py-2">
                @endif
                <select name="discount_type" class="rounded-lg border border-border px-3 py-2"><option value="percentage">Percentage</option><option value="fixed">Fixed amount</option></select>
                <input type="number" name="discount_value" min="0" step="0.01" required placeholder="Discount value" class="rounded-lg border border-border px-3 py-2">
                @if ($module === 'discount-codes')<input type="number" name="usage_limit" min="1" placeholder="Usage limit" class="rounded-lg border border-border px-3 py-2">@endif
                <input type="date" name="starts_on" required class="rounded-lg border border-border px-3 py-2">
                <input type="date" name="ends_on" required class="rounded-lg border border-border px-3 py-2">
                <select name="status" class="rounded-lg border border-border px-3 py-2">@if($module === 'promotions')<option value="draft">Draft</option>@endif<option value="active">Active</option><option value="{{ $module === 'promotions' ? 'expired' : 'inactive' }}">{{ $module === 'promotions' ? 'Expired' : 'Inactive' }}</option></select>
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:-translate-y-0.5 hover:bg-secondary">Save record</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">{{ $module === 'promotions' ? 'Promotion' : 'Code' }}</th><th class="px-5 py-3.5">Discount</th><th class="px-5 py-3.5">Validity</th><th class="px-5 py-3.5">Usage</th><th class="px-5 py-3.5">Status</th></tr></thead><tbody class="divide-y divide-border">@forelse($records as $record)<tr class="transition hover:bg-background/70"><td class="px-5 py-4 font-medium text-primary">{{ $module === 'promotions' ? $record->name : $record->code }}</td><td class="px-5 py-4">{{ $record->discount_type === 'percentage' ? $record->discount_value . '%' : 'PHP ' . number_format($record->discount_value, 2) }}</td><td class="px-5 py-4">{{ $record->starts_on->format('d M Y') }} - {{ $record->ends_on->format('d M Y') }}</td><td class="px-5 py-4">{{ $module === 'discount-codes' ? ($record->used_count . ($record->usage_limit ? ' / ' . $record->usage_limit : '')) : '—' }}</td><td class="px-5 py-4"><span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold capitalize text-accent">{{ $record->status }}</span></td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No records yet.</td></tr>@endforelse</tbody></table><div class="border-t border-border px-5 py-4">{{ $records->links() }}</div></div>
    @elseif ($module === 'calendar')
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Forward view</p><h3 class="mt-1 font-heading text-xl font-semibold text-primary">Marketing calendar</h3><p class="mt-2 text-sm text-gray-500">Review campaign timing, channels, budgets, and status in one view.</p></div>
        <div class="space-y-3">@forelse($records as $campaign)<div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-wide text-secondary">{{ $campaign->start_date->format('d M Y') }} - {{ $campaign->end_date->format('d M Y') }}</p><h3 class="font-heading text-lg font-semibold text-primary">{{ $campaign->name }}</h3></div><span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold capitalize text-accent">{{ $campaign->status }}</span></div><div class="mt-3 grid gap-3 text-sm text-gray-600 sm:grid-cols-3"><div>Channel<br><strong class="capitalize text-primary">{{ str_replace('_', ' ', $campaign->channel) }}</strong></div><div>Budget<br><strong class="text-primary">PHP {{ number_format($campaign->budget, 2) }}</strong></div><div>Leads / conversions<br><strong class="text-primary">{{ $campaign->leads_generated }} / {{ $campaign->conversions }}</strong></div></div></div>@empty<div class="rounded-2xl border border-border bg-card p-10 text-center text-gray-400">No campaigns scheduled.</div>@endforelse</div><div>{{ $records->links() }}</div>
    @else
        <style>
            div:has(> table thead th:nth-child(5)) thead { background: linear-gradient(90deg, rgba(22, 59, 109, 0.08), rgba(245, 155, 69, 0.08)); }
            div:has(> table thead th:nth-child(5)) th { padding-top: .9rem; padding-bottom: .9rem; font-size: .68rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; white-space: nowrap; }
            div:has(> table thead th:nth-child(5)) tbody tr { transition: background-color 180ms ease; }
            div:has(> table thead th:nth-child(5)) tbody tr:hover { background-color: rgba(111, 169, 230, 0.08); }
            div:has(> table thead th:nth-child(5)) tbody td { padding-top: 1rem; padding-bottom: 1rem; vertical-align: middle; font-variant-numeric: tabular-nums; }
            div:has(> table thead th:nth-child(5)) tbody td:nth-child(2) { color: #f59b45; font-weight: 700; white-space: nowrap; }
            div:has(> table thead th:nth-child(5)) tbody td:nth-child(3), div:has(> table thead th:nth-child(5)) tbody td:nth-child(4) { color: #6fa9e6; font-weight: 700; }
            div:has(> table thead th:nth-child(5)) tbody td:nth-child(5) { color: #22c55e; font-weight: 700; white-space: nowrap; }
            .dark div:has(> table thead th:nth-child(5)) tbody td:first-child { color: #f9fafb; }
        </style>
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Performance view</p><h3 class="mt-1 font-heading text-xl font-semibold text-primary">Campaign analytics</h3><p class="mt-2 text-sm text-gray-500">Compare marketing spend, lead generation, and conversion performance.</p></div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"><div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Total spend</div><div class="mt-2 text-2xl font-semibold text-primary">PHP {{ number_format($analytics['spend'], 2) }}</div></div><div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Budget</div><div class="mt-2 text-2xl font-semibold text-primary">PHP {{ number_format($analytics['budget'], 2) }}</div></div><div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Leads</div><div class="mt-2 text-2xl font-semibold text-primary">{{ number_format($analytics['leads']) }}</div></div><div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Conversions</div><div class="mt-2 text-2xl font-semibold text-primary">{{ number_format($analytics['conversions']) }}</div></div></div>
        <div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="text-gray-500"><tr><th class="px-5 py-3">Channel</th><th class="px-5 py-3">Spend</th><th class="px-5 py-3">Leads</th><th class="px-5 py-3">Conversions</th><th class="px-5 py-3">Conversion rate</th></tr></thead><tbody class="divide-y divide-border">@forelse($analytics['byChannel'] as $row)<tr><td class="px-5 py-3 font-medium capitalize text-primary">{{ str_replace('_', ' ', $row->channel) }}</td><td class="px-5 py-3">PHP {{ number_format($row->spend, 2) }}</td><td class="px-5 py-3">{{ $row->leads }}</td><td class="px-5 py-3">{{ $row->conversions }}</td><td class="px-5 py-3">{{ $row->leads > 0 ? number_format(($row->conversions / $row->leads) * 100, 1) : '0.0' }}%</td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No campaign data yet.</td></tr>@endforelse</tbody></table></div>
    @endif
</div>
@endsection
