@extends('layouts.app')
@section('title', 'Add Tour Package')

@section('content')
<div class="max-w-3xl rounded-xl border border-border bg-card p-6 shadow-sm">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Tour inventory</p>
        <h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Add tour package</h2>
        <p class="mt-2 text-sm text-gray-500">Create a package with its destination, pricing, and available slots.</p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-error/20 bg-error/10 p-4 text-sm text-error" role="alert">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('packages.store') }}" class="space-y-5">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Package name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="Bali 5D4N Family Tour">
            </div>

            <div>
                <label for="type" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                <select id="type" name="type" required class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="domestic" @selected(old('type', 'domestic') === 'domestic')>Domestic</option>
                    <option value="international" @selected(old('type') === 'international')>International</option>
                </select>
            </div>

            <div>
                <label for="destination" class="mb-1 block text-sm font-medium text-gray-700">Destination</label>
                <input id="destination" type="text" name="destination" value="{{ old('destination') }}" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="Bali, Indonesia">
            </div>

            <div>
                <label for="price" class="mb-1 block text-sm font-medium text-gray-700">Price</label>
                <input id="price" type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="0.00">
            </div>

            <div>
                <label for="duration_days" class="mb-1 block text-sm font-medium text-gray-700">Duration (days)</label>
                <input id="duration_days" type="number" name="duration_days" value="{{ old('duration_days') }}" min="1" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="5">
            </div>

            <div>
                <label for="slots" class="mb-1 block text-sm font-medium text-gray-700">Available slots</label>
                <input id="slots" type="number" name="slots" value="{{ old('slots', 0) }}" min="0" class="w-full rounded-lg border border-border px-3 py-2" placeholder="0">
            </div>

            <div class="md:col-span-2">
                <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-border px-3 py-2" placeholder="Describe the itinerary and inclusions">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-white">Save package</button>
            <a href="{{ route('packages.index') }}" class="rounded-lg border border-border px-4 py-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
