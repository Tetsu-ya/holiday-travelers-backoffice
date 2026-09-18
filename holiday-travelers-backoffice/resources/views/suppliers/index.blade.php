@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Supplier network</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Supplier directory</h2><p class="mt-2 text-sm text-gray-500">Track hotel, transport, and service providers by reliability and rate.</p></div><a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ Add supplier</a></div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl border border-border border-t-4 border-t-primary p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Total suppliers</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $supplierStats['total'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Active</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $supplierStats['active'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Needs review</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $supplierStats['needs_review'] }}</div>
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
                            <td class="px-5 py-4 capitalize">{{ $supplier->category }}</td>
                            <td class="px-5 py-4">{{ $supplier->location ?? '—' }}</td>
                            <td class="px-5 py-4">₱{{ number_format($supplier->base_rate ?? 0, 2) }}</td>
                            <td class="px-5 py-4">{{ $supplier->reliability_rating ?? 0 }}%</td>
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