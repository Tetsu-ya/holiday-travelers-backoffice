@extends('layouts.app')
@section('title', 'Edit Campaign')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Campaign name</label>
                <input type="text" name="name" required value="{{ $campaign->name }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Channel</label>
                <select name="channel" class="w-full rounded-lg border border-border px-3 py-2">
                    @foreach(['email','social_media','referral','ads','events'] as $channel)
                        <option value="{{ $channel }}" {{ $campaign->channel === $channel ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tour package</label>
                <select name="tour_package_id" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="">Select package</option>
                    @foreach (\App\Models\TourPackage::orderBy('name')->get() as $package)
                        <option value="{{ $package->id }}" {{ $campaign->tour_package_id == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Budget</label>
                <input type="number" step="0.01" min="0" name="budget" value="{{ $campaign->budget }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Actual spend</label>
                <input type="number" step="0.01" min="0" name="actual_spend" value="{{ $campaign->actual_spend }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Start date</label>
                <input type="date" name="start_date" value="{{ $campaign->start_date }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">End date</label>
                <input type="date" name="end_date" value="{{ $campaign->end_date }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full rounded-lg border border-border px-3 py-2">
                    @foreach(['planned','active','completed','paused'] as $status)
                        <option value="{{ $status }}" {{ $campaign->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Leads generated</label>
                <input type="number" min="0" name="leads_generated" value="{{ $campaign->leads_generated }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Conversions</label>
                <input type="number" min="0" name="conversions" value="{{ $campaign->conversions }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Update campaign</button>
            <a href="{{ route('campaigns.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
