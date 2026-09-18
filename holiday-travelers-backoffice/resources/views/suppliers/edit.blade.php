@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-5">
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
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Update supplier</button>
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
