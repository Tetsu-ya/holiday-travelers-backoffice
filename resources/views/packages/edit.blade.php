@extends('layouts.app')
@section('title', 'Edit Tour Package')
@section('content')
<div class="max-w-3xl rounded-xl border border-border bg-card p-6 shadow-sm">
    <div class="mb-6"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Tour inventory</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Edit tour package</h2><p class="mt-2 text-sm text-gray-500">Update the package destination, pricing, and availability.</p></div>
    @if ($errors->any())<div class="mb-5 rounded-lg border border-error/20 bg-error/10 p-4 text-sm text-error"><p class="font-semibold">Please correct the following:</p><ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('packages.update', $package) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">Package name</label><input type="text" name="name" value="{{ old('name', $package->name) }}" required class="w-full rounded-lg border border-border px-3 py-2"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Type</label><select name="type" required class="w-full rounded-lg border border-border px-3 py-2"><option value="domestic" @selected(old('type', $package->type) === 'domestic')>Domestic</option><option value="international" @selected(old('type', $package->type) === 'international')>International</option></select></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Destination</label><input type="text" name="destination" value="{{ old('destination', $package->destination) }}" required class="w-full rounded-lg border border-border px-3 py-2"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Price</label><input type="number" name="price" value="{{ old('price', $package->price) }}" min="0" step="0.01" required class="w-full rounded-lg border border-border px-3 py-2"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Duration (days)</label><input type="number" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" min="1" required class="w-full rounded-lg border border-border px-3 py-2"></div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Available slots</label><input type="number" name="slots" value="{{ old('slots', $package->slots) }}" min="0" class="w-full rounded-lg border border-border px-3 py-2"></div>
            <div class="md:col-span-2"><label class="mb-1 block text-sm font-medium text-gray-700">Description</label><textarea name="description" rows="4" class="w-full rounded-lg border border-border px-3 py-2">{{ old('description', $package->description) }}</textarea></div>
        </div>
        <div class="flex gap-3"><button type="submit" class="rounded-lg bg-primary px-4 py-2 text-white">Update package</button><a href="{{ route('packages.index') }}" class="rounded-lg border border-border px-4 py-2">Cancel</a></div>
    </form>
</div>
@endsection
