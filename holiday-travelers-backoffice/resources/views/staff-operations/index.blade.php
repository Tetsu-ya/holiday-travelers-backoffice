@extends('layouts.app')
@section('title', $label)
@section('content')
<div class="space-y-6">
	<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
		<div>
			<p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Team operations</p>
			<h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">{{ $label }}</h2>
			<p class="mt-2 text-sm text-gray-500">Manage {{ strtolower($label) }} for your back-office team.</p>
		</div>
		<div class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">{{ $records->total() }} records</div>
	</div>

	<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
		<div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-5 py-4">
			<p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Quick entry</p>
			<h3 class="mt-1 font-heading font-semibold text-primary">Add {{ str()->singular($label) }}</h3>
		</div>
		<form method="POST" action="{{ route($slug . '.store') }}" class="grid gap-4 p-5 md:grid-cols-3">
			@csrf
			@if ($slug === 'tasks')
				<input name="title" required placeholder="Task title" class="rounded-xl border border-border bg-background px-3 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
				<select name="assigned_to" required class="rounded-lg border border-border px-3 py-2">
					@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
				</select>
				<select name="priority" class="rounded-lg border border-border px-3 py-2"><option value="normal">Normal</option><option value="low">Low</option><option value="high">High</option></select>
				<input type="date" name="due_date" class="rounded-lg border border-border px-3 py-2">
				<select name="status" class="rounded-lg border border-border px-3 py-2"><option value="todo">To do</option><option value="in_progress">In progress</option><option value="done">Done</option></select>
			@elseif ($slug === 'scheduling')
				<select name="user_id" required class="rounded-lg border border-border px-3 py-2">
					@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
				</select>
				<input type="date" name="schedule_date" required class="rounded-lg border border-border px-3 py-2">
				<input type="time" name="starts_at" class="rounded-lg border border-border px-3 py-2">
				<input type="time" name="ends_at" class="rounded-lg border border-border px-3 py-2">
				<input name="location" placeholder="Location" class="rounded-lg border border-border px-3 py-2">
			@elseif ($slug === 'performance')
				<select name="user_id" required class="rounded-lg border border-border px-3 py-2">
					@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
				</select>
				<input name="period" required placeholder="2026-09" class="rounded-lg border border-border px-3 py-2">
				<input type="number" name="score" min="0" max="100" required placeholder="Score" class="rounded-lg border border-border px-3 py-2">
				<input type="number" name="bookings_completed" min="0" required placeholder="Bookings completed" class="rounded-lg border border-border px-3 py-2">
				<input type="number" name="revenue_generated" min="0" step="0.01" required placeholder="Revenue" class="rounded-lg border border-border px-3 py-2">
			@else
				<select name="user_id" required class="rounded-lg border border-border px-3 py-2">
					@foreach ($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
				</select>
				<input name="period" required placeholder="2026-09" class="rounded-lg border border-border px-3 py-2">
				<input type="number" name="basis_amount" min="0" step="0.01" required placeholder="Basis amount" class="rounded-lg border border-border px-3 py-2">
				<input type="number" name="rate" min="0" max="100" step="0.01" required placeholder="Rate %" class="rounded-lg border border-border px-3 py-2">
				<select name="status" class="rounded-lg border border-border px-3 py-2"><option value="pending">Pending</option><option value="approved">Approved</option><option value="paid">Paid</option></select>
			@endif
			<button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:-translate-y-0.5 hover:bg-secondary">Add record</button>
		</form>
	</div>

	<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
		<table class="w-full text-left text-sm">
			<thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Staff member</th><th class="px-5 py-3.5">Details</th><th class="px-5 py-3.5">Status</th><th class="px-5 py-3.5">Created</th></tr></thead>
			<tbody class="divide-y divide-border">
				@forelse ($records as $record)
					<tr class="transition hover:bg-background/70">
						<td class="px-5 py-4"><div class="font-medium text-primary">{{ ($record->user ?? $record->assignee)?->name }}</div><div class="mt-0.5 text-xs text-gray-400">Team member</div></td>
						<td class="px-5 py-3">
							@if ($slug === 'tasks')
								{{ $record->title }} · Due {{ $record->due_date?->format('d M Y') ?: 'not set' }}
							@elseif ($slug === 'scheduling')
								{{ $record->schedule_date?->format('d M Y') }} · {{ $record->starts_at ?: 'flexible' }}
							@elseif ($slug === 'performance')
								{{ $record->period }} · Score {{ $record->score }} · {{ $record->bookings_completed }} bookings
							@else
								{{ $record->period }} · PHP {{ number_format($record->amount, 2) }}
							@endif
						</td>
						<td class="px-5 py-4"><span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold capitalize text-accent">{{ $record->status ?? 'recorded' }}</span></td>
						<td class="px-5 py-4 text-gray-500">{{ $record->created_at->diffForHumans() }}</td>
					</tr>
				@empty
					<tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No records yet.</td></tr>
				@endforelse
			</tbody>
		</table>
		<div class="border-t border-border px-5 py-4">{{ $records->links() }}</div>
	</div>
</div>
@endsection
{{--
@extends('layouts.app')
@section('title', $label)
@section('content')
<div class="space-y-6"><div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"><div><h2 class="font-heading text-xl font-semibold text-primary">{{ $label }}</h2><p class="text-sm text-gray-500">Manage {{ strtolower($label) }} for your back-office team.</p></div></div><div class="rounded-2xl border border-border bg-card p-5 shadow-sm"><h3 class="mb-4 font-heading font-semibold text-primary">Add {{ str()->singular($label) }}</h3><form method="POST" action="{{ route($slug.'.store') }}" class="grid gap-4 md:grid-cols-3">@csrf@if($slug === 'tasks')<input name="title" required placeholder="Task title" class="rounded-lg border border-border px-3 py-2"><select name="assigned_to" required class="rounded-lg border border-border px-3 py-2">@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><select name="priority" class="rounded-lg border border-border px-3 py-2"><option>normal</option><option>low</option><option>high</option></select><input type="date" name="due_date" class="rounded-lg border border-border px-3 py-2"><select name="status" class="rounded-lg border border-border px-3 py-2"><option value="todo">To do</option><option value="in_progress">In progress</option><option value="done">Done</option></select>@elseif($slug === 'scheduling')<select name="user_id" required class="rounded-lg border border-border px-3 py-2">@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><input type="date" name="schedule_date" required class="rounded-lg border border-border px-3 py-2"><input type="time" name="starts_at" class="rounded-lg border border-border px-3 py-2"><input type="time" name="ends_at" class="rounded-lg border border-border px-3 py-2"><input name="location" placeholder="Location" class="rounded-lg border border-border px-3 py-2">@elseif($slug === 'performance')<select name="user_id" required class="rounded-lg border border-border px-3 py-2">@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><input name="period" required placeholder="2026-09" class="rounded-lg border border-border px-3 py-2"><input type="number" name="score" min="0" max="100" required placeholder="Score" class="rounded-lg border border-border px-3 py-2"><input type="number" name="bookings_completed" min="0" required placeholder="Bookings completed" class="rounded-lg border border-border px-3 py-2"><input type="number" name="revenue_generated" min="0" step="0.01" required placeholder="Revenue" class="rounded-lg border border-border px-3 py-2">@else<select name="user_id" required class="rounded-lg border border-border px-3 py-2">@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select><input name="period" required placeholder="2026-09" class="rounded-lg border border-border px-3 py-2"><input type="number" name="basis_amount" min="0" step="0.01" required placeholder="Basis amount" class="rounded-lg border border-border px-3 py-2"><input type="number" name="rate" min="0" max="100" step="0.01" required placeholder="Rate %" class="rounded-lg border border-border px-3 py-2"><select name="status" class="rounded-lg border border-border px-3 py-2"><option>pending</option><option>approved</option><option>paid</option></select>@endif<button class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white">Add record</button></form></div><div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm"><table class="w-full text-left text-sm"><thead class="text-gray-500"><tr><th class="px-5 py-3">Staff member</th><th class="px-5 py-3">Details</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Created</th></tr></thead><tbody class="divide-y divide-border">@forelse($records as $record)<tr><td class="px-5 py-3 font-medium text-primary">{{ ($record->user ?? $record->assignee)?->name }}</td><td class="px-5 py-3">@if($slug === 'tasks'){{ $record->title }} · Due {{ $record->due_date?->format('d M Y') ?: 'not set' }}@elseif($slug === 'scheduling'){{ $record->schedule_date?->format('d M Y') }} · {{ $record->starts_at ?: 'flexible' }}@elseif($slug === 'performance'){{ $record->period }} · Score {{ $record->score }} · {{ $record->bookings_completed }} bookings@else{{ $record->period }} · PHP {{ number_format($record->amount, 2) }}@endif</td><td class="px-5 py-3 capitalize">{{ $record->status ?? 'recorded' }}</td><td class="px-5 py-3 text-gray-500">{{ $record->created_at->diffForHumans() }}</td></tr>@empty<tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No records yet.</td></tr>@endforelse</tbody></table><div class="border-t border-border px-5 py-4">{{ $records->links() }}</div></div></div>
@endsection
--}}
