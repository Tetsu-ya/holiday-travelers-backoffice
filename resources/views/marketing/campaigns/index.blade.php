@extends('layouts.app')
@section('title', 'Marketing Campaigns')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Marketing operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Marketing campaigns</h2><p class="mt-2 text-sm text-gray-500">Track performance across paid, organic, referral, and email channels.</p></div><a href="{{ route('campaigns.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ New campaign</a></div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl border border-border border-t-4 border-t-primary p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Active campaigns</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $campaignStats['active'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Budget used</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">₱{{ number_format($campaignStats['budget_used'], 2) }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Leads generated</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ number_format($campaignStats['leads']) }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Conversion rate</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ number_format($campaignStats['conversion_rate'], 1) }}%</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Directory</p><h2 class="mt-1 font-heading font-semibold text-primary text-xl">All campaigns</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="campaign-directory w-full text-sm">
                <thead class="bg-background text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">Name</th>
                        <th class="px-5 py-3.5">Channel</th>
                        <th class="px-5 py-3.5">Budget</th>
                        <th class="px-5 py-3.5">Leads</th>
                        <th class="px-5 py-3.5">Conversions</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($campaigns as $campaign)
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-4 font-medium text-primary">{{ $campaign->name }}</td>
                            <td class="px-5 py-4"><span class="inline-flex items-center rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold capitalize text-accent">{{ str_replace('_', ' ', $campaign->channel) }}</span></td>
                            <td class="px-5 py-4">₱{{ number_format($campaign->budget, 2) }}</td>
                            <td class="px-5 py-4"><span class="inline-flex min-w-10 justify-center rounded-lg bg-primary/5 px-2.5 py-1 font-semibold text-primary">{{ number_format((int) $campaign->leads_generated) }}</span></td>
                            <td class="px-5 py-4"><span class="inline-flex min-w-10 justify-center rounded-lg bg-success/10 px-2.5 py-1 font-semibold text-success">{{ number_format((int) $campaign->conversions) }}</span></td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if (($campaign->status ?? 'active') === 'active') bg-success/10 text-success
                                    @elseif (($campaign->status ?? 'active') === 'paused') bg-warning/10 text-warning
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($campaign->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="text-xs font-medium text-primary">View</a>
                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="text-xs font-medium text-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-gray-400">No campaigns found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border px-5 py-4">{{ $campaigns->links() }}</div>
    </div>
</div>
@endsection
