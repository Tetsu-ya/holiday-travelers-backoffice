@extends('layouts.app')
@section('title', 'Edit Business Partner')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('partners.store') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required value="{{ $partner->name }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="w-full rounded-lg border border-border px-3 py-2">
                    @foreach(['agency','corporate','affiliate'] as $type)
                        <option value="{{ $type }}" {{ $partner->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Contact person</label>
                <input type="text" name="contact_person" value="{{ $partner->contact_person }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ $partner->email }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ $partner->phone }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Region</label>
                <input type="text" name="region" value="{{ $partner->region }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Commission rate (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ $partner->commission_rate ?? 0 }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Update partner</button>
            <a href="{{ route('partners.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
