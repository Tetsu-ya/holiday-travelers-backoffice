@extends('layouts.app')
@section('title', 'Create Campaign')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('campaigns.store') }}" class="space-y-5" id="campaign-create-form">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Campaign name</label>
                <input type="text" name="name" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="Summer Escape Promo">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Channel</label>
                <select name="channel" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="email">Email</option>
                    <option value="social_media">Social media</option>
                    <option value="referral">Referral</option>
                    <option value="ads">Ads</option>
                    <option value="events">Events</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Tour package</label>
                <select name="tour_package_id" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="">Select package</option>
                    @foreach (\App\Models\TourPackage::orderBy('name')->get() as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Budget</label>
                <input type="number" step="0.01" min="0" name="budget" class="w-full rounded-lg border border-border px-3 py-2" value="0">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Actual spend</label>
                <input type="number" step="0.01" min="0" name="actual_spend" class="w-full rounded-lg border border-border px-3 py-2" value="0">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Start date</label>
                <input type="date" name="start_date" required class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">End date</label>
                <input type="date" name="end_date" required class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="planned">Planned</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Leads generated</label>
                <input type="number" min="0" name="leads_generated" class="w-full rounded-lg border border-border px-3 py-2" value="0">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Conversions</label>
                <input type="number" min="0" name="conversions" class="w-full rounded-lg border border-border px-3 py-2" value="0">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="campaign-submit group inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-secondary hover:shadow-xl active:translate-y-0 active:scale-95"><span class="campaign-submit-label">Create campaign</span><span aria-hidden="true" class="transition-transform duration-200 group-hover:translate-x-1">→</span></button>
            <a href="{{ route('campaigns.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-border px-5 py-2.5 text-sm font-semibold text-primary transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary active:translate-y-0 active:scale-95"><span class="transition-transform duration-200 group-hover:-translate-x-1">←</span><span>Cancel</span></a>
        </div>
    </form>
</div>
<script>
    document.getElementById('campaign-create-form')?.addEventListener('submit', function () {
        const button = this.querySelector('.campaign-submit');
        if (!button) return;
        button.disabled = true;
        button.classList.add('is-loading');
        button.querySelector('.campaign-submit-label').textContent = 'Creating campaign...';
    });
</script>
@endsection
