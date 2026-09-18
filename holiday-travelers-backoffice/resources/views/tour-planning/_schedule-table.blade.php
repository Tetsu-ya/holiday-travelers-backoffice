<div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Tour package</th><th class="px-5 py-3.5">Date</th><th class="px-5 py-3.5">Time</th><th class="px-5 py-3.5">Capacity</th><th class="px-5 py-3.5">Location</th><th class="px-5 py-3.5">Status</th></tr></thead>
        <tbody class="divide-y divide-border">
            @forelse ($schedules as $schedule)
                <tr class="transition hover:bg-background/70"><td class="px-5 py-4 font-medium text-primary">{{ $schedule->tourPackage->name }}</td><td class="px-5 py-4">{{ $schedule->schedule_date->format('d M Y') }}</td><td class="px-5 py-4">{{ $schedule->starts_at ?: 'Flexible' }}{{ $schedule->ends_at ? ' - ' . $schedule->ends_at : '' }}</td><td class="px-5 py-4">{{ $schedule->capacity }}</td><td class="px-5 py-4">{{ $schedule->location ?: 'Not set' }}</td><td class="px-5 py-4"><span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold capitalize text-accent">{{ $schedule->status }}</span></td></tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No tour schedules yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-border px-5 py-4">{{ $schedules->links() }}</div>
</div>
