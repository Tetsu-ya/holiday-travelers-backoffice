@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Supplier network</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Supplier directory</h2><p class="mt-2 text-sm text-gray-500">Track hotel, transport, and service providers by reliability and rate.</p></div><a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ Add supplier</a></div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-card rounded-2xl border border-border border-t-4 border-t-primary p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Total suppliers</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $supplierStats['total'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Active</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $supplierStats['active'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Avg. reliability</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ number_format($supplierStats['average_reliability'], 1) }}%</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Directory</p><h2 class="mt-1 font-heading font-semibold text-primary text-xl">All suppliers</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-background text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">Supplier</th>
                        <th class="px-5 py-3.5">Category</th>
                        <th class="px-5 py-3.5">Location</th>
                        <th class="px-5 py-3.5">Base rate</th>
                        <th class="px-5 py-3.5">Reliability</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($suppliers as $supplier)
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-4 font-medium text-primary">{{ $supplier->name }}</td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full bg-primary/5 px-2.5 py-1 text-xs font-semibold capitalize text-primary">{{ str_replace('_', ' ', $supplier->category) }}</span></td>
                            <td class="px-5 py-4"><div class="flex items-center gap-2 text-gray-600"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-accent/10 text-xs text-accent">⌖</span><span>{{ $supplier->location ?? 'Location not set' }}</span></div></td>
                            <td class="px-5 py-4"><span class="font-semibold text-primary">₱{{ number_format($supplier->base_rate ?? 0, 2) }}</span><span class="mt-0.5 block text-[11px] text-gray-400">Starting rate</span></td>
                            <td class="px-5 py-4"><div class="min-w-28"><div class="mb-1 flex items-center justify-between gap-2"><span class="text-sm font-semibold text-primary">{{ number_format(($supplier->reliability_rating ?? 0) * 20, 0) }}%</span><span class="text-[11px] text-gray-400">reliability</span></div><div class="h-1.5 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-success" style="width: {{ min(($supplier->reliability_rating ?? 0) * 20, 100) }}%"></div></div></div></td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if (($supplier->status ?? 'active') === 'active') bg-success/10 text-success
                                    @elseif (($supplier->status ?? 'active') === 'inactive') bg-gray-100 text-gray-600
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($supplier->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="text-xs font-medium text-primary">View</a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="text-xs font-medium text-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-gray-400">No suppliers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border px-5 py-4">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
