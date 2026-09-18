@extends('layouts.app')
@section('title', $module === 'availability' ? 'Supplier Availability' : 'Supplier ' . ucfirst($module))
@section('content')
@php
    $titles = ['contracts' => 'Supplier Contracts', 'rates' => 'Supplier Rates', 'availability' => 'Supplier Availability', 'performance' => 'Supplier Performance'];
@endphp
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Supplier operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">{{ $titles[$module] }}</h2><p class="mt-2 text-sm text-gray-500">Manage {{ strtolower($titles[$module]) }} across your supplier network.</p></div>
        <div class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">{{ $records->total() }} records</div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-5 py-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Quick entry</p><h3 class="mt-1 font-heading font-semibold text-primary">Add record</h3></div>
        <form method="POST" action="{{ route('supplier-' . $module . '.store') }}" class="grid gap-4 p-5 md:grid-cols-3">
            @csrf
            <select name="supplier_id" required class="rounded-lg border border-border px-3 py-2"><option value="">Select supplier</option>@foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach</select>
            @if ($module === 'contracts')
                <input name="contract_number" required placeholder="Contract number" class="rounded-lg border border-border px-3 py-2"><input type="date" name="starts_on" required class="rounded-lg border border-border px-3 py-2"><input type="date" name="ends_on" class="rounded-lg border border-border px-3 py-2"><input type="number" name="value" min="0" step="0.01" placeholder="Contract value" class="rounded-lg border border-border px-3 py-2"><select name="status" class="rounded-lg border border-border px-3 py-2"><option value="active">Active</option><option value="draft">Draft</option><option value="expired">Expired</option></select>
            @elseif ($module === 'rates')
                <input name="service" required placeholder="Service" class="rounded-lg border border-border px-3 py-2"><input type="number" name="rate" min="0" step="0.01" required placeholder="Rate" class="rounded-lg border border-border px-3 py-2"><input name="unit" required value="per booking" placeholder="Unit" class="rounded-lg border border-border px-3 py-2"><input type="date" name="effective_from" required class="rounded-lg border border-border px-3 py-2"><input type="date" name="effective_until" class="rounded-lg border border-border px-3 py-2">
            @elseif ($module === 'availability')
                <input type="date" name="available_on" required class="rounded-lg border border-border px-3 py-2"><input type="number" name="capacity" min="0" required placeholder="Capacity" class="rounded-lg border border-border px-3 py-2"><select name="status" class="rounded-lg border border-border px-3 py-2"><option value="available">Available</option><option value="limited">Limited</option><option value="unavailable">Unavailable</option></select>
            @else
                <input name="period" required placeholder="2026-09" class="rounded-lg border border-border px-3 py-2"><input type="number" name="score" min="0" max="100" step="0.01" required placeholder="Score %" class="rounded-lg border border-border px-3 py-2"><input type="number" name="bookings_completed" min="0" required placeholder="Bookings completed" class="rounded-lg border border-border px-3 py-2">
            @endif
            <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:-translate-y-0.5 hover:bg-secondary">Save record</button>
        </form>
    </div>
    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Supplier</th><th class="px-5 py-3.5">Details</th><th class="px-5 py-3.5">Status</th><th class="px-5 py-3.5">Created</th></tr></thead><tbody class="divide-y divide-border">
        @forelse ($records as $record)
            <tr class="transition hover:bg-background/70"><td class="px-5 py-4"><div class="font-medium text-primary">{{ $record->supplier->name }}</div><div class="mt-0.5 text-xs text-gray-400">Supplier partner</div></td><td class="px-5 py-4">
                @if ($module === 'contracts')
                    {{ $record->contract_number }} · {{ $record->starts_on->format('d M Y') }}
                @elseif ($module === 'rates')
                    {{ $record->service }} · PHP {{ number_format($record->rate, 2) }} {{ $record->unit }}
                @elseif ($module === 'availability')
                    {{ $record->available_on->format('d M Y') }} · Capacity {{ $record->capacity }}
                @else
                    {{ $record->period }} · Score {{ $record->score }} · {{ $record->bookings_completed }} bookings
                @endif
            </td><td class="px-5 py-4"><span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold capitalize text-accent">{{ $record->status ?? 'recorded' }}</span></td><td class="px-5 py-4 text-gray-500">{{ $record->created_at->diffForHumans() }}</td></tr>
        @empty
            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No records yet.</td></tr>
        @endforelse
    </tbody></table><div class="border-t border-border px-5 py-4">{{ $records->links() }}</div></div>
</div>
@endsection
