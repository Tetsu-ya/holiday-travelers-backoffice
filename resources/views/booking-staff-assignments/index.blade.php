@extends('layouts.app')
@section('title', 'Staff Assignments')

@section('content')
<div class="max-w-7xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">People operations</p>
            <h1 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Staff assignments</h1>
            <p class="mt-2 text-sm text-gray-500">Assign the right staff member to each client booking and track their readiness.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-primary">View bookings</a>
    </div>

    <div class="rounded-2xl border border-border bg-card p-5 shadow-sm">
        <h2 class="font-heading text-lg font-semibold text-primary">Assign staff to a booking</h2>
        <p class="mt-1 text-sm text-gray-500">A booking can have multiple staff members with different responsibilities.</p>
        <form method="POST" action="{{ route('booking-staff-assignments.store') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @csrf
            <select name="booking_id" required class="rounded-lg border border-border bg-card px-3 py-2 xl:col-span-2">
                <option value="">Select client booking</option>
                @foreach($bookingOptions as $booking)
                    <option value="{{ $booking->id }}" @selected($selectedBooking === $booking->id)>{{ $booking->reference_no }} · {{ $booking->customer_name }} · {{ $booking->travel_date?->format('d M Y') }}</option>
                @endforeach
            </select>
            <select name="user_id" required class="rounded-lg border border-border bg-card px-3 py-2">
                <option value="">Select staff member</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}{{ $user->job_title ? ' · '.$user->job_title : '' }}</option>
                @endforeach
            </select>
            <select name="assignment_role" required class="rounded-lg border border-border bg-card px-3 py-2">
                <option value="">Select role</option>
                <option value="travel_agent">Travel agent</option>
                <option value="tour_guide">Tour guide</option>
                <option value="driver">Driver</option>
                <option value="coordinator">Tour coordinator</option>
                <option value="visa_officer">Visa officer</option>
                <option value="other">Other</option>
            </select>
            <select name="status" class="rounded-lg border border-border bg-card px-3 py-2">
                <option value="assigned">Assigned</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
            </select>
            <textarea name="notes" rows="1" placeholder="Notes (optional)" class="rounded-lg border border-border px-3 py-2 md:col-span-2 xl:col-span-4"></textarea>
            <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">Assign staff</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-border px-5 py-5">
            <div><h2 class="font-heading text-lg font-semibold text-primary">Upcoming bookings</h2><p class="mt-1 text-sm text-gray-500">Manage the people assigned to each client trip.</p></div>
            <span class="hidden rounded-full bg-secondary/10 px-3 py-1.5 text-xs font-semibold text-secondary sm:inline-flex">{{ $bookings->total() }} bookings</span>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[680px] text-left text-sm">
            <thead class="border-b border-border bg-background/70 text-[11px] font-semibold uppercase tracking-[0.16em] text-gray-500"><tr><th class="w-[28%] px-5 py-4">Booking</th><th class="w-[34%] px-5 py-4">Client / trip</th><th class="w-[38%] px-5 py-4">Assigned staff</th></tr></thead>
            <tbody class="divide-y divide-border">
                @forelse($bookings as $booking)
                    <tr class="align-top transition-colors hover:bg-background/40">
                        <td class="px-5 py-5"><a href="{{ route('bookings.show', $booking) }}" class="font-semibold tracking-tight text-secondary transition hover:text-accent">{{ $booking->reference_no }}</a><div class="mt-1.5 flex items-center gap-1.5 text-xs text-gray-500"><span class="h-1.5 w-1.5 rounded-full bg-accent"></span>{{ $booking->travel_date?->format('M d, Y') }}</div></td>
                        <td class="px-5 py-5"><div class="font-semibold text-primary">{{ $booking->customer_name }}</div><div class="mt-1.5 max-w-[260px] truncate text-xs text-gray-500" title="{{ $booking->tourPackage->name ?? 'Package unavailable' }}">{{ $booking->tourPackage->name ?? 'Package unavailable' }} <span class="text-gray-600">·</span> {{ $booking->pax }} pax</div></td>
                        <td class="px-5 py-5">
                            @forelse($booking->staffAssignments as $assignment)
                                <div class="group mb-2 flex items-center justify-between gap-3 rounded-xl border border-border bg-card px-3 py-3 shadow-sm transition hover:border-secondary/40 hover:shadow-md last:mb-0">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-secondary/10 text-sm font-bold text-secondary">{{ str($assignment->user->name)->substr(0, 1)->upper() }}</span>
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-primary">{{ $assignment->user->name }}</div>
                                            <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-gray-500">
                                                <span class="capitalize">{{ str_replace('_', ' ', $assignment->assignment_role) }}</span>
                                                <span class="h-1 w-1 rounded-full bg-gray-400"></span>
                                                <span class="rounded-full bg-success/10 px-2 py-0.5 font-semibold capitalize text-success">{{ $assignment->status }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('booking-staff-assignments.destroy', $assignment) }}" class="shrink-0">@csrf @method('DELETE')<button class="rounded-lg border border-error/20 px-2.5 py-1.5 text-xs font-semibold text-error transition hover:border-error/40 hover:bg-error/10 focus:outline-none focus:ring-4 focus:ring-error/10">Remove</button></form>
                                </div>
                            @empty
                                <div class="flex items-center gap-2 text-gray-500"><span class="flex h-7 w-7 items-center justify-center rounded-full border border-dashed border-gray-600 text-xs">—</span><span>No staff assigned</span></div>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400">No upcoming bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="border-t border-border px-5 py-4">{{ $bookings->links() }}</div>
    </div>
</div>
@endsection
