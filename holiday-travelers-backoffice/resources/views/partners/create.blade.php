@extends('layouts.app')
@section('title', 'Add Business Partner')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('partners.store') }}" class="space-y-5">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="TravelHub Asia">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="agency">Agency</option>
                    <option value="corporate">Corporate</option>
                    <option value="affiliate">Affiliate</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Contact person</label>
                <input type="text" name="contact_person" class="w-full rounded-lg border border-border px-3 py-2" placeholder="Jane Doe">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="w-full rounded-lg border border-border px-3 py-2" placeholder="partner@example.com">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" class="w-full rounded-lg border border-border px-3 py-2" placeholder="+63...">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Region</label>
                <input type="text" name="region" class="w-full rounded-lg border border-border px-3 py-2" placeholder="Metro Manila">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Commission rate (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="commission_rate" class="w-full rounded-lg border border-border px-3 py-2" value="0">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Save partner</button>
            <a href="{{ route('partners.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
