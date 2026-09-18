@extends('layouts.app')
@section('title', 'Add Lead')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('leads.store') }}" class="space-y-5">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="Maria Santos">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Campaign</label>
                <select name="marketing_campaign_id" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="">General lead</option>
                    @foreach (\App\Models\MarketingCampaign::orderBy('name')->get() as $campaign)
                        <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="w-full rounded-lg border border-border px-3 py-2" placeholder="lead@example.com">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" class="w-full rounded-lg border border-border px-3 py-2" placeholder="+63...">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Interested package</label>
                <input type="text" name="interested_package" class="w-full rounded-lg border border-border px-3 py-2" placeholder="Bali 5D4N Family Tour">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full rounded-lg border border-border px-3 py-2">
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="converted">Converted</option>
                    <option value="lost">Lost</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Save lead</button>
            <a href="{{ route('leads.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
