@extends('layouts.app')
@section('title', 'Leads')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">New leads</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $leadStats['new'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Qualified</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $leadStats['qualified'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Booked</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $leadStats['converted'] }}</div>
        </div>
        <div class="bg-card rounded-2xl border border-border p-4 shadow-sm">
            <div class="text-xs text-gray-500">Conversion</div>
            <div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ number_format($leadStats['conversion_rate'], 1) }}%</div>
        </div>
    </div>

    <div class="bg-card rounded-2xl border border-border p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
            <div>
                <h2 class="font-heading font-semibold text-primary text-xl">Lead pipeline</h2>
                <p class="text-sm text-gray-500">Track incoming inquiries from source to confirmed booking.</p>
            </div>
            <a href="{{ route('leads.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium">+ Add lead</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-5">
            @php $stages = ['New Lead','Contacted','Qualified','Lost','Converted']; @endphp
            @foreach ($stages as $stage)
                <div class="rounded-xl border border-border bg-gray-50 p-3 text-center">
                    <div class="text-xs text-gray-500">{{ $stage }}</div>
                    <div class="mt-2 text-xl font-semibold text-primary">
                        {{ [
                            $leadStats['new'],
                            $leadStats['contacted'],
                            $leadStats['qualified'],
                            $leadStats['lost'],
                            $leadStats['converted'],
                        ][array_search($stage, $stages)] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500">
                    <tr>
                        <th class="pb-3 pr-4">Name</th>
                        <th class="pb-3 pr-4">Email</th>
                        <th class="pb-3 pr-4">Phone</th>
                        <th class="pb-3 pr-4">Interested package</th>
                        <th class="pb-3 pr-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($leads as $lead)
                        <tr>
                            <td class="py-3 pr-4 font-medium text-primary">{{ $lead->name }}</td>
                            <td class="py-3 pr-4">{{ $lead->email }}</td>
                            <td class="py-3 pr-4">{{ $lead->phone }}</td>
                            <td class="py-3 pr-4">{{ $lead->interested_package ?? '—' }}</td>
                            <td class="py-3 pr-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if (($lead->status ?? 'new') === 'booked') bg-success/10 text-success
                                    @elseif (($lead->status ?? 'new') === 'qualified') bg-secondary/10 text-secondary
                                    @elseif (($lead->status ?? 'new') === 'contacted') bg-warning/10 text-warning
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($lead->status ?? 'new') }}
                                </span>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('leads.show', $lead) }}" class="text-xs font-medium text-primary">View</a>
                                    <a href="{{ route('leads.edit', $lead) }}" class="text-xs font-medium text-secondary">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-gray-400">No leads found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $leads->links() }}</div>
    </div>
</div>
@endsection