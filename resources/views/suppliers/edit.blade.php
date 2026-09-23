@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Supplier name</label>
                <input type="text" name="name" required value="{{ $supplier->name }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                <select name="category" class="w-full rounded-lg border border-border px-3 py-2">
                    @foreach(['hotel','airline','transport','tour_guide','other'] as $category)
                        <option value="{{ $category }}" {{ $supplier->category === $category ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $category)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Contact person</label>
                <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ $supplier->email }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" value="{{ $supplier->location }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Base rate</label>
                <input type="number" step="0.01" min="0" name="base_rate" value="{{ $supplier->base_rate ?? 0 }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Reliability rating (0–5)</label>
                <input type="number" step="0.01" min="0" max="5" name="reliability_rating" value="{{ $supplier->reliability_rating ?? 0 }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition-all duration-200 hover:-translate-y-0.5 hover:bg-secondary hover:shadow-xl active:translate-y-0 active:scale-95"><span>Update supplier</span><span class="transition-transform duration-200 group-hover:translate-x-1">→</span></button>
            <a href="{{ route('suppliers.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-primary transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary active:translate-y-0 active:scale-95"><span class="transition-transform duration-200 group-hover:-translate-x-1">←</span><span>Cancel</span></a>
        </div>
    </form>
</div>
@endsection
