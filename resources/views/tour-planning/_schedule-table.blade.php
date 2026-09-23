<div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Tour package</th><th class="px-5 py-3.5">Date</th><th class="px-5 py-3.5">Time</th><th class="px-5 py-3.5">Capacity</th><th class="px-5 py-3.5">Location</th><th class="px-5 py-3.5">Status</th></tr></thead>
        <tbody class="divide-y divide-border">
            @forelse ($schedules as $schedule)
                <tr class="transition hover:bg-background/70"><td class="px-5 py-4"><div class="font-semibold text-primary">{{ $schedule->tourPackage->name }}</div><div class="mt-1 text-xs text-gray-400">Tour package</div></td><td class="px-5 py-4"><span class="inline-flex items-center gap-2 rounded-lg bg-background px-2.5 py-1.5 font-medium text-primary"><span class="text-xs text-accent">▣</span>{{ $schedule->schedule_date->format('d M Y') }}</span></td><td class="px-5 py-4"><span class="inline-flex rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-semibold text-secondary">{{ $schedule->starts_at ? \Illuminate\Support\Carbon::parse($schedule->starts_at)->format('g:i A') : 'Flexible' }}{{ $schedule->ends_at ? ' – ' . \Illuminate\Support\Carbon::parse($schedule->ends_at)->format('g:i A') : '' }}</span></td><td class="px-5 py-4"><span class="inline-flex rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold text-accent">{{ number_format($schedule->capacity) }} seats</span></td><td class="px-5 py-4"><div class="flex items-center gap-2 text-gray-600"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/5 text-xs text-primary">⌖</span><span>{{ $schedule->location ?: 'Location not set' }}</span></div></td><td class="px-5 py-4"><span class="rounded-full {{ $schedule->status === 'confirmed' ? 'bg-success/10 text-success' : ($schedule->status === 'cancelled' ? 'bg-error/10 text-error' : 'bg-accent/10 text-accent') }} px-2.5 py-1 text-xs font-semibold capitalize">{{ $schedule->status }}</span></td></tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No tour schedules yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="border-t border-border px-5 py-4">{{ $schedules->links() }}</div>
</div>
