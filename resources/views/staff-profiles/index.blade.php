@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">People operations</p>
            <h1 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Staff management</h1>
            <p class="mt-2 text-sm text-gray-500">Manage travel agents, guides, drivers, roles, availability, and client-trip assignments.</p>
        </div>
        <a href="{{ route('staff-profiles.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ Add staff</a>
    </div>

    <div class="grid grid-cols-2 gap-4 xl:grid-cols-5">
        @foreach ([
            ['Total staff', $staffStats['total'], 'All staff profiles'],
            ['Tour guides', $staffStats['tour_guides'], 'Guide assignments'],
            ['Drivers', $staffStats['drivers'], 'Transport team'],
            ['Travel agents', $staffStats['travel_agents'], 'Customer-facing team'],
            ['Others', $staffStats['others'], 'Managers, admins, and staff'],
        ] as [$label, $value, $hint])
            <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.1em] text-gray-500">{{ $label }}</p>
                <p class="mt-2 font-heading text-2xl font-semibold text-primary">{{ $value }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <form method="GET" class="flex flex-col gap-3 border-b border-border bg-background/50 p-4 sm:flex-row sm:items-center">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search staff, role, phone, or department" class="min-w-0 rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-primary focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/10">
            <select name="role" class="rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-primary">
                <option value="">All roles</option>
                @foreach (['admin' => 'Administrator', 'manager' => 'Manager', 'agent' => 'Travel agent', 'tour_guide' => 'Tour guide', 'driver' => 'Driver', 'staff' => 'Staff'] as $value => $label)
                    <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-primary">
                <option value="">All status</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
            <button class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-secondary">Search</button>
            @if ($search || $role || $status)
                <a href="{{ route('staff-profiles.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-5 py-2.5 text-center text-sm font-semibold text-gray-600 shadow-sm transition hover:border-secondary hover:bg-secondary/5 hover:text-secondary focus:outline-none focus:ring-4 focus:ring-secondary/10">
                    <span aria-hidden="true" class="text-base leading-none">×</span>
                    Clear filters
                </a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <tr>
                        <th class="px-5 py-3.5">ID</th>
                        <th class="px-5 py-3.5">Name</th>
                        <th class="px-5 py-3.5">Role</th>
                        <th class="px-5 py-3.5">Contact</th>
                        <th class="px-5 py-3.5">Assigned trip / client</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($staff as $member)
                        @php($assignment = $member->bookingStaffAssignments->first())
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-4 text-xs font-semibold text-gray-500">STF-{{ str_pad($member->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-secondary/10 text-xs font-bold text-secondary">{{ str($member->name)->substr(0, 1)->upper() }}</span>
                                    <div><div class="font-semibold text-primary">{{ $member->name }}</div><div class="mt-0.5 text-xs text-gray-400">{{ $member->email }}</div></div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-primary">{{ $member->job_title ?: str_replace('_', ' ', ucfirst($member->role ?: 'Staff')) }}</span>
                                <span class="mt-1 block text-xs capitalize text-gray-400">{{ $member->department ?: 'No department' }}</span>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">{{ $member->phone ?: 'No phone provided' }}</td>
                            <td class="px-5 py-4">
                                @if ($assignment)
                                    <a href="{{ route('bookings.show', $assignment->booking) }}" class="font-medium text-secondary hover:text-accent">{{ $assignment->booking->tourPackage?->name ?: 'Package unavailable' }}</a>
                                    <span class="mt-1 block text-xs text-gray-400">{{ $assignment->booking->customer_name }} · {{ str_replace('_', ' ', $assignment->assignment_role) }}</span>
                                @else
                                    <span class="text-xs text-gray-400">No current assignment</span>
                                @endif
                            </td>
                            <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($member->status ?? 'active') === 'active' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">{{ ucfirst($member->status ?? 'active') }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('staff-profiles.show', $member) }}" class="text-xs font-semibold text-primary hover:text-secondary">View</a>
                                    <a href="{{ route('staff-profiles.edit', $member) }}" class="text-xs font-semibold text-secondary hover:text-accent">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No staff profiles match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-border px-5 py-4">{{ $staff->links() }}</div>
    </div>
</div>
@endsection
