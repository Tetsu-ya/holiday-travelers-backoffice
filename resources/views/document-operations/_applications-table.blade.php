@php
    $statusStyles = [
        'draft' => 'text-gray-400',
        'submitted' => 'text-success',
        'processing' => 'text-secondary',
        'approved' => 'text-success',
        'rejected' => 'text-error',
    ];
@endphp
<div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="bg-background/60 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-400"><tr><th class="px-5 py-3.5">Customer</th><th class="px-5 py-3.5">Country</th><th class="px-5 py-3.5">Visa type</th><th class="px-5 py-3.5">Travel date</th><th class="px-5 py-3.5">Status</th></tr></thead><tbody class="divide-y divide-border">@forelse($records as $record)<tr class="transition hover:bg-background/70"><td class="px-5 py-4 font-semibold text-primary">{{ $record->customer_name }}</td><td class="px-5 py-4 font-medium text-secondary">{{ $record->country }}</td><td class="px-5 py-4 font-medium text-secondary">{{ $record->visa_type }}</td><td class="px-5 py-4 text-gray-500">{{ $record->travel_date?->format('d M Y') ?: '—' }}</td><td class="px-5 py-4"><span class="font-semibold capitalize {{ $statusStyles[$record->status] ?? 'text-gray-500' }}">{{ $record->status }}</span></td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No visa applications yet.</td></tr>@endforelse</tbody></table><div class="border-t border-border px-5 py-4">{{ $records->links() }}</div></div>
