@extends('layouts.app')
@section('title', 'Create Social Ad')

@section('content')
<div class="max-w-3xl space-y-5">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-secondary">Marketing hub / Social ads</p>
        <h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Create social ad campaign</h2>
        <p class="mt-2 text-sm text-gray-500">Set up a campaign and track its leads and conversions.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-error/20 bg-error/10 p-4 text-sm text-error" role="alert">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('campaigns.store') }}" class="space-y-5 rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="mb-1.5 block text-sm font-medium text-primary">Campaign name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm" placeholder="Summer island getaway">
            </div>
            <div>
                <label for="channel" class="mb-1.5 block text-sm font-medium text-primary">Social platform</label>
                <select id="channel" name="channel" required class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
                    <option value="facebook" @selected(old('channel', request('channel', 'facebook')) === 'facebook')>Facebook</option>
                    <option value="instagram" @selected(old('channel', request('channel')) === 'instagram')>Instagram</option>
                    <option value="tiktok" @selected(old('channel', request('channel')) === 'tiktok')>TikTok</option>
                </select>
            </div>
            <div>
                <label for="tour_package_id" class="mb-1.5 block text-sm font-medium text-primary">Promoted tour package <span class="font-normal text-gray-400">(optional)</span></label>
                <select id="tour_package_id" name="tour_package_id" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
                    <option value="">No specific package</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}" @selected((string) old('tour_package_id') === (string) $package->id)>{{ $package->name }} — {{ $package->destination }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="marketing_brochure_id" class="mb-1.5 block text-sm font-medium text-primary">Promotional brochure <span class="font-normal text-gray-400">(optional)</span></label>
                <select id="marketing_brochure_id" name="marketing_brochure_id" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
                    <option value="">No brochure attached</option>
                    @foreach ($brochures as $brochure)
                        <option value="{{ $brochure->id }}" @selected((string) old('marketing_brochure_id') === (string) $brochure->id)>{{ $brochure->title }} — {{ $brochure->file_name }}</option>
                    @endforeach
                </select>
                @if ($brochures->isEmpty())
                    <p class="mt-1.5 text-xs text-gray-500">No brochures uploaded yet. <a href="{{ route('campaign-analytics.index', ['tab' => 'brochures']) }}" class="font-semibold text-secondary hover:underline">Upload one in Marketing Hub</a>.</p>
                @else
                    <p class="mt-1.5 text-xs text-gray-500">Choose an uploaded brochure to associate it with this social ad.</p>
                @endif
            </div>
            <div>
                <label for="start_date" class="mb-1.5 block text-sm font-medium text-primary">Start date</label>
                <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
            </div>
            <div>
                <label for="end_date" class="mb-1.5 block text-sm font-medium text-primary">End date</label>
                <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-primary">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
                    @foreach (['planned', 'active', 'paused', 'completed'] as $status)
                        <option value="{{ $status }}" @selected(old('status', 'planned') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="leads_generated" class="mb-1.5 block text-sm font-medium text-primary">Leads generated</label>
                <input id="leads_generated" type="number" min="0" name="leads_generated" value="{{ old('leads_generated', 0) }}" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
            </div>
            <div>
                <label for="conversions" class="mb-1.5 block text-sm font-medium text-primary">Conversions</label>
                <input id="conversions" type="number" min="0" name="conversions" value="{{ old('conversions', 0) }}" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm">
            </div>
        </div>
        <div class="flex flex-wrap gap-3 border-t border-border pt-5">
            <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary">Save social ad</button>
            <a href="{{ route('campaign-analytics.index', ['tab' => 'social-ads']) }}" class="rounded-xl border border-border px-5 py-2.5 text-sm font-semibold text-primary transition hover:border-secondary hover:text-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
