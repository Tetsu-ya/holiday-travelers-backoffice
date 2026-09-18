@extends('layouts.app')
@section('title', 'Staff Profiles')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">People operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Staff directory</h2><p class="mt-2 text-sm text-gray-500">Manage the people who operate the Holiday Travelers back office.</p></div>
        <a href="{{ route('staff-profiles.create') }}" class="inline-flex items-center justify-center rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5">+ Add staff member</a>
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-border border-t-4 border-t-primary bg-card p-4 shadow-sm"><div class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Total staff</div><div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $staffStats['total'] }}</div></div>
        <div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Active</div><div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $staffStats['active'] }}</div></div>
        <div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Administrators</div><div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $staffStats['admins'] }}</div></div>
        <div class="rounded-2xl border border-border bg-card p-4 shadow-sm"><div class="text-xs text-gray-500">Departments</div><div class="mt-2 text-3xl font-heading font-semibold text-primary">{{ $staffStats['departments'] }}</div></div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex flex-col gap-4 border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Directory</p>
                <h2 class="mt-1 font-heading text-xl font-semibold text-primary">All staff members</h2>
            </div>
        </div>

        <form method="GET" class="flex flex-col gap-3 border-b border-border bg-background/50 p-4 sm:flex-row">
            <input type="search" name="search" value="{{ $search }}" placeholder="Search name, email, or department" class="min-w-0 flex-1 rounded-xl border border-border bg-card px-3 py-2.5 text-sm text-primary focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
            <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Search</button>
            @if ($search)
                <a href="{{ route('staff-profiles.index') }}" class="rounded-lg border border-border px-4 py-2 text-center text-sm">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Staff member</th><th class="px-5 py-3.5">Role</th><th class="px-5 py-3.5">Department</th><th class="px-5 py-3.5">Status</th><th class="px-5 py-3.5">Actions</th></tr></thead>
                <tbody class="divide-y divide-border">
                    @forelse ($staff as $member)
                        <tr class="transition hover:bg-background/70">
                            <td class="px-5 py-3"><div class="font-medium text-primary">{{ $member->name }}</div><div class="text-xs text-gray-500">{{ $member->email }}</div></td>
                            <td class="px-5 py-3 capitalize">{{ $member->role ?? 'staff' }}</td>
                            <td class="px-5 py-3">{{ $member->department ?: '—' }}</td>
                            <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($member->status ?? 'active') === 'active' ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($member->status ?? 'active') }}</span></td>
                            <td class="px-5 py-3"><div class="flex items-center gap-3"><a href="{{ route('staff-profiles.show', $member) }}" class="text-xs font-medium text-primary">View</a><a href="{{ route('staff-profiles.edit', $member) }}" class="text-xs font-medium text-secondary">Edit</a></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No staff profiles found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-border px-5 py-4">{{ $staff->links() }}</div>
    </div>
</div>
@endsection
