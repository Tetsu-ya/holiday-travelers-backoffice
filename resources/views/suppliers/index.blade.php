@extends('layouts.app')
@section('title', 'Suppliers & Partners')

@section('content')
@php
    $tabs = [
        'all' => ['All', $supplierStats['total']],
        'hotels' => ['Hotels', $supplierStats['hotels']],
        'transportation' => ['Transportation', $supplierStats['transportation']],
        'tour-activities' => ['Tour Activities', $supplierStats['tour_activities']],
        'services' => ['Services', $supplierStats['services']],
    ];
    $categoryLabels = ['hotel' => 'Hotel', 'airline' => 'Transport', 'transport' => 'Transport', 'tour_guide' => 'Tour Activity', 'other' => 'Service'];
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Partner network</p><h1 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Suppliers &amp; Partners</h1><p class="mt-2 text-sm text-gray-500">Manage the hotels, transport providers, tour activities, and services behind every trip.</p></div>
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:bg-primary">+ Add supplier/partner</a>
    </div>

    <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
        @foreach ([['Total suppliers', $supplierStats['total'], 'All providers', 'text-primary'], ['Hotels', $supplierStats['hotels'], 'Accommodation', 'text-success'], ['Transport', $supplierStats['transportation'], 'Transfers & flights', 'text-secondary'], ['Tour activities', $supplierStats['tour_activities'], 'Guides & activities', 'text-accent'], ['Services', $supplierStats['services'], 'Other services', 'text-secondary']] as [$label, $value, $hint, $color])
            <div class="rounded-2xl border border-border bg-card p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"><p class="text-xs font-semibold uppercase tracking-[0.1em] text-gray-500">{{ $label }}</p><p class="mt-2 font-heading text-2xl font-semibold {{ $color }}">{{ $value }}</p><p class="mt-1 text-xs text-gray-400">{{ $hint }}</p></div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-wrap gap-2 border-b border-border bg-background/50 p-4">
            @foreach ($tabs as $value => [$label, $count])
                <a href="{{ route('suppliers.index', array_filter(['category' => $value === 'all' ? null : $value, 'search' => $search, 'status' => $status])) }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold transition {{ $category === $value ? 'bg-secondary text-white shadow-sm' : 'bg-card text-gray-600 hover:bg-secondary/10 hover:text-secondary' }}">{{ $label }} <span class="{{ $category === $value ? 'text-white/80' : 'text-gray-400' }}">{{ $count }}</span></a>
            @endforeach
        </div>

        <form method="GET" class="flex flex-col gap-3 border-b border-border p-4 md:flex-row md:items-center">
            <input type="hidden" name="category" value="{{ $category }}">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search supplier, partner or agreement..." class="min-w-0 rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-primary focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/10">
            <select name="type" class="rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-primary md:w-40"><option value="">All types</option><option value="hotel" @selected($type === 'hotel')>Hotels</option><option value="transport" @selected($type === 'transport')>Transportation</option><option value="tour_guide" @selected($type === 'tour_guide')>Tour activities</option><option value="other" @selected($type === 'other')>Services</option></select>
            <select name="status" class="rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-primary md:w-40"><option value="active" @selected($status === 'active')>Active</option><option value="inactive" @selected($status === 'inactive')>Inactive</option><option value="blacklisted" @selected($status === 'blacklisted')>Blacklisted</option><option value="" @selected($status === '')>All statuses</option></select>
            <button class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary">Search</button>
        </form>

        <div class="overflow-x-auto"><table class="w-full min-w-[1050px] text-left text-sm"><thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Partner name</th><th class="px-5 py-3.5">Type</th><th class="px-5 py-3.5">Phone</th><th class="px-5 py-3.5">Reliability</th><th class="px-5 py-3.5">Agreement valid until</th><th class="px-5 py-3.5">Perks / inclusions</th><th class="px-5 py-3.5 text-right">Action</th></tr></thead>
            <tbody class="divide-y divide-border">
                @forelse ($suppliers as $supplier)
                    <tr class="transition hover:bg-background/70"><td class="px-5 py-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-sm font-bold text-secondary">{{ str($supplier->name)->substr(0, 1)->upper() }}</span><div class="min-w-0"><a href="{{ route('suppliers.show', $supplier) }}" class="block truncate font-semibold text-primary hover:text-secondary">{{ $supplier->name }}</a><span class="text-xs text-gray-400">{{ $supplier->location ?: 'Location not set' }}</span></div></div></td><td class="px-5 py-4"><span class="inline-flex rounded-full bg-primary/5 px-2.5 py-1 text-xs font-semibold text-primary">{{ $categoryLabels[$supplier->category] ?? ucfirst($supplier->category) }}</span></td><td class="whitespace-nowrap px-5 py-4 text-gray-600">{{ $supplier->phone ?: 'Not provided' }}</td><td class="px-5 py-4"><div class="w-28"><div class="mb-1 flex items-center justify-between text-xs"><span class="font-semibold text-primary">{{ number_format(($supplier->reliability_rating ?? 0) * 20, 0) }}%</span><span class="text-gray-400">score</span></div><div class="h-1.5 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-secondary" style="width: {{ min(($supplier->reliability_rating ?? 0) * 20, 100) }}%"></div></div></div></td><td class="whitespace-nowrap px-5 py-4"><div class="font-semibold {{ $supplier->agreement_valid_until && $supplier->agreement_valid_until->isFuture() ? 'text-success' : 'text-gray-500' }}">{{ $supplier->agreement_valid_until ? 'Valid until '.$supplier->agreement_valid_until->format('M d, Y') : 'Date not set' }}</div></td><td class="px-5 py-4 text-gray-600">{{ $supplier->perks_inclusions ?: 'No perks or inclusions recorded' }}</td><td class="px-5 py-4"><div class="flex items-center justify-end gap-3"><a href="{{ route('suppliers.show', $supplier) }}" class="text-xs font-semibold text-primary hover:text-secondary">View</a><a href="{{ route('suppliers.edit', $supplier) }}" class="text-xs font-semibold text-secondary hover:text-primary">Edit</a></div></td></tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">No suppliers match these filters.</td></tr>
                @endforelse
            </tbody></table></div>
        <div class="border-t border-border px-5 py-4">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
