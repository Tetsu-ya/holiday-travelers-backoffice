@extends('layouts.app')
@section('title', 'Edit Lead')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required value="{{ $lead->name }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Campaign</label>
                <select name="marketing_campaign_id" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="">General lead</option>
                    @foreach (\App\Models\MarketingCampaign::orderBy('name')->get() as $campaign)
                        <option value="{{ $campaign->id }}" {{ $lead->marketing_campaign_id == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ $lead->email }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ $lead->phone }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Interested package</label>
                <input type="text" name="interested_package" value="{{ $lead->interested_package }}" class="w-full rounded-lg border border-border px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full rounded-lg border border-border px-3 py-2">
                    @foreach(['new','contacted','qualified','converted','lost'] as $status)
                        <option value="{{ $status }}" {{ $lead->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Update lead</button>
            <a href="{{ route('leads.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
