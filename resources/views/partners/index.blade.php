@extends('layouts.app')
@section('title', 'Business Partners')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Partnerships</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Business partners</h2><p class="mt-2 text-sm text-gray-500">Manage agencies, corporate accounts, and affiliate relationships.</p></div><a href="{{ route('partners.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ Add partner</a></div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl border border-border border-t-4 border-t-primary p-4 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Partners</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $partnerStats['total'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Top region</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $partnerStats['top_region'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Avg. commission</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ number_format($partnerStats['average_commission'], 2) }}%</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Active</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $partnerStats['active'] }}</div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Directory</p><h2 class="mt-1 font-heading font-semibold text-primary text-xl">All partners</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-background text-left text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">Name</th>
                        <th class="px-5 py-3.5">Type</th>
                        <th class="px-5 py-3.5">Region</th>
                        <th class="px-5 py-3.5">Commission</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($partners as $partner)
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-4 font-medium text-primary">{{ $partner->name }}</td>
                            <td class="px-5 py-4 capitalize">{{ $partner->type }}</td>
                            <td class="px-5 py-4">{{ $partner->region ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $partner->commission_rate ?? 0 }}%</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if (($partner->status ?? 'active') === 'active') bg-success/10 text-success
                                    @elseif (($partner->status ?? 'active') === 'inactive') bg-gray-100 text-gray-600
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($partner->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('partners.show', $partner) }}" class="text-xs font-medium text-primary">View</a>
                                    <a href="{{ route('partners.edit', $partner) }}" class="text-xs font-medium text-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-gray-400">No partners found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border px-5 py-4">{{ $partners->links() }}</div>
    </div>
</div>
@endsection