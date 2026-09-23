@extends('layouts.app')
@section('title', match ($module) {
    'schedule' => 'Tour Schedule',
    'availability' => 'Tour Availability',
    'allocation' => 'Resource Allocation',
    'assignment' => 'Staff Assignment',
    default => 'Resource Calendar',
})

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Tour operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">{{ match ($module) { 'schedule' => 'Tour Schedule', 'availability' => 'Tour Availability', 'allocation' => 'Resource Allocation', 'assignment' => 'Staff Assignment', default => 'Resource Calendar' } }}</h2><p class="mt-2 text-sm text-gray-500">Coordinate departures, capacity, resources, and staff from one workspace.</p></div>
        <span class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">Live operations</span>
    </div>
    @if ($module === 'schedule')
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
            <div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-5 py-4"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Quick entry</p><h3 class="mt-1 font-heading font-semibold text-primary">Add tour schedule</h3></div>
            <form method="POST" action="{{ route('tour-schedule.store') }}" class="grid gap-4 p-5 md:grid-cols-3">
                @csrf
                <select name="tour_package_id" required class="rounded-lg border border-border px-3 py-2"><option value="">Select tour package</option>@foreach ($packages as $package)<option value="{{ $package->id }}">{{ $package->name }}</option>@endforeach</select>
                <input type="date" name="schedule_date" required class="rounded-lg border border-border px-3 py-2">
                <input type="number" name="capacity" min="1" required placeholder="Capacity" class="rounded-lg border border-border px-3 py-2">
                <input type="time" name="starts_at" class="rounded-lg border border-border px-3 py-2">
                <input type="time" name="ends_at" class="rounded-lg border border-border px-3 py-2">
                <input name="location" placeholder="Meeting point" class="rounded-lg border border-border px-3 py-2">
                <select name="status" class="rounded-lg border border-border px-3 py-2"><option value="planned">Planned</option><option value="confirmed">Confirmed</option><option value="cancelled">Cancelled</option><option value="completed">Completed</option></select>
                <input name="notes" placeholder="Notes" class="rounded-lg border border-border px-3 py-2 md:col-span-2">
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:-translate-y-0.5 hover:bg-secondary">Add schedule</button>
            </form>
        </div>
        @include('tour-planning._schedule-table', ['schedules' => $schedules])
    @elseif ($module === 'availability')
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Capacity monitor</p><h3 class="mt-1 font-heading text-xl font-semibold text-primary">Tour availability</h3><p class="mt-2 text-sm text-gray-500">Live capacity based on package slots and active bookings.</p></div>
        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Package</th><th class="px-5 py-3.5">Destination</th><th class="px-5 py-3.5">Capacity</th><th class="px-5 py-3.5">Booked</th><th class="px-5 py-3.5">Available</th><th class="px-5 py-3.5">Status</th></tr></thead><tbody class="divide-y divide-border">@forelse ($packages as $package)@php $available = max(0, (int) $package->slots - (int) ($package->booked_pax ?? 0)); $capacity = max((int) $package->slots, 1); $fill = min(100, (($package->booked_pax ?? 0) / $capacity) * 100); @endphp<tr class="transition hover:bg-background/70"><td class="px-5 py-4"><div class="font-semibold text-primary">{{ $package->name }}</div><div class="mt-1 text-xs text-gray-400">Tour package</div></td><td class="px-5 py-4"><div class="flex items-center gap-2 text-gray-600"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-accent/10 text-xs text-accent">⌖</span><span>{{ $package->destination }}</span></div></td><td class="px-5 py-4"><span class="rounded-full bg-primary/5 px-2.5 py-1 text-xs font-semibold text-primary">{{ number_format($package->slots) }} seats</span></td><td class="px-5 py-4"><div class="min-w-24"><div class="mb-1 flex justify-between text-xs"><span class="font-semibold text-primary">{{ number_format($package->booked_pax ?? 0) }}</span><span class="text-gray-400">{{ number_format($fill, 0) }}%</span></div><div class="h-1.5 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full bg-secondary" style="width: {{ $fill }}%"></div></div></div></td><td class="px-5 py-4"><span class="rounded-full {{ $available > 0 ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }} px-2.5 py-1 text-xs font-semibold">{{ number_format($available) }} available</span></td><td class="px-5 py-4"><span class="rounded-full {{ $package->status === 'active' ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600' }} px-2.5 py-1 text-xs font-semibold capitalize">{{ $package->status }}</span></td></tr>@empty<tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No tour packages found.</td></tr>@endforelse</tbody></table><div class="border-t border-border px-5 py-4">{{ $packages->links() }}</div></div>
    @elseif ($module === 'allocation')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('form[action*="resource-allocation"]').forEach((form) => {
                    form.addEventListener('submit', () => {
                        const button = form.querySelector('button[type="submit"], button:not([type])');
                        if (!button) return;
                        button.disabled = true;
                        button.textContent = 'Allocating...';
                    });
                });
            });
        </script>
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><h2 class="font-heading text-xl font-semibold text-primary">Resource allocation</h2><p class="mb-5 text-sm text-gray-500">Reserve transport, rooms, guides, and other resources for a scheduled tour.</p><form method="POST" action="{{ route('resource-allocation.store') }}" class="grid gap-4 md:grid-cols-3">@csrf<select name="tour_schedule_id" required class="rounded-lg border border-border px-3 py-2"><option value="">Select scheduled tour</option>@foreach($schedules as $schedule)<option value="{{ $schedule->id }}">{{ $schedule->schedule_date->format('d M Y') }} · {{ $schedule->tourPackage->name }}</option>@endforeach</select><input name="resource_type" required placeholder="Resource type" class="rounded-lg border border-border px-3 py-2"><input name="resource_name" required placeholder="Resource name" class="rounded-lg border border-border px-3 py-2"><input type="number" name="quantity" min="1" value="1" required class="rounded-lg border border-border px-3 py-2"><select name="status" class="rounded-lg border border-border px-3 py-2"><option value="reserved">Reserved</option><option value="requested">Requested</option><option value="confirmed">Confirmed</option></select><button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white">Allocate resource</button></form></div><div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="text-gray-500"><tr><th class="px-5 py-3">Tour</th><th class="px-5 py-3">Resource</th><th class="px-5 py-3">Quantity</th><th class="px-5 py-3">Status</th></tr></thead><tbody class="divide-y divide-border">@forelse($allocations as $allocation)<tr><td class="px-5 py-3">{{ $allocation->tourSchedule->tourPackage->name }} · {{ $allocation->tourSchedule->schedule_date->format('d M Y') }}</td><td class="px-5 py-3 font-medium text-primary">{{ $allocation->resource_name }} <span class="text-xs text-gray-500">({{ $allocation->resource_type }})</span></td><td class="px-5 py-3">{{ $allocation->quantity }}</td><td class="px-5 py-3 capitalize">{{ $allocation->status }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No allocations yet.</td></tr>@endforelse</tbody></table><div class="border-t border-border px-5 py-4">{{ $allocations->links() }}</div></div>
    @elseif ($module === 'assignment')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('form[action*="staff-assignment"]').forEach((form) => {
                    form.addEventListener('submit', () => {
                        const button = form.querySelector('button[type="submit"], button:not([type])');
                        if (!button) return;
                        button.disabled = true;
                        button.textContent = 'Assigning...';
                    });
                });
            });
        </script>
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><h2 class="font-heading text-xl font-semibold text-primary">Staff assignment</h2><p class="mb-5 text-sm text-gray-500">Assign coordinators and agents to upcoming tours.</p><form method="POST" action="{{ route('staff-assignment.store') }}" class="grid gap-4 md:grid-cols-3">@csrf<select name="tour_schedule_id" required class="rounded-lg border border-border px-3 py-2"><option value="">Select scheduled tour</option>@foreach($schedules as $schedule)<option value="{{ $schedule->id }}">{{ $schedule->schedule_date->format('d M Y') }} · {{ $schedule->tourPackage->name }}</option>@endforeach</select><select name="user_id" required class="rounded-lg border border-border px-3 py-2"><option value="">Select staff member</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><input name="assignment_role" required value="tour_coordinator" placeholder="Assignment role" class="rounded-lg border border-border px-3 py-2"><select name="status" class="rounded-lg border border-border px-3 py-2"><option value="assigned">Assigned</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option></select><button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white">Assign staff</button></form></div><div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="text-gray-500"><tr><th class="px-5 py-3">Tour</th><th class="px-5 py-3">Staff member</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Status</th></tr></thead><tbody class="divide-y divide-border">@forelse($assignments as $assignment)<tr><td class="px-5 py-3">{{ $assignment->tourSchedule->tourPackage->name }} · {{ $assignment->tourSchedule->schedule_date->format('d M Y') }}</td><td class="px-5 py-3 font-medium text-primary">{{ $assignment->user->name }}</td><td class="px-5 py-3">{{ $assignment->assignment_role }}</td><td class="px-5 py-3 capitalize">{{ $assignment->status }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No staff assignments yet.</td></tr>@endforelse</tbody></table><div class="border-t border-border px-5 py-4">{{ $assignments->links() }}</div></div>
    @else
        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Forward view</p><h3 class="mt-1 font-heading text-xl font-semibold text-primary">Resource calendar</h3><p class="mt-2 text-sm text-gray-500">Upcoming tours with their staff and allocated resources.</p></div>
        <div class="space-y-3">@forelse($schedules as $schedule)<div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-wide text-secondary">{{ $schedule->schedule_date->format('D, d M Y') }}</p><h3 class="font-heading text-lg font-semibold text-primary">{{ $schedule->tourPackage->name }}</h3></div><span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold capitalize text-accent">{{ $schedule->status }}</span></div><div class="mt-4 grid gap-3 text-sm text-gray-600 md:grid-cols-3"><div><span class="text-gray-400">Location</span><br>{{ $schedule->location ?: 'Not set' }}</div><div><span class="text-gray-400">Staff</span><br>{{ $schedule->assignments->pluck('user.name')->filter()->join(', ') ?: 'Unassigned' }}</div><div><span class="text-gray-400">Resources</span><br>{{ $schedule->allocations->map(fn($item) => $item->resource_name . ' × ' . $item->quantity)->join(', ') ?: 'None allocated' }}</div></div></div>@empty<div class="rounded-2xl border border-border bg-card p-10 text-center text-gray-400">No upcoming tours scheduled.</div>@endforelse</div><div>{{ $schedules->links() }}</div>
    @endif
</div>
@endsection
