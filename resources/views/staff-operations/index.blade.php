@extends('layouts.app')
@section('title', $label)
@section('content')
<div class="space-y-6">
	@if ($errors->any())
		<div class="rounded-xl border border-error/20 bg-error/5 px-4 py-3 text-sm text-error">
			<p class="font-semibold">Changes could not be saved.</p>
			<ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
		</div>
	@endif
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
			<thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Staff member</th><th class="px-5 py-3.5">Details</th><th class="px-5 py-3.5">Status</th><th class="px-5 py-3.5">Created</th>@if (in_array($slug, ['tasks', 'scheduling', 'performance']))<th class="px-5 py-3.5 text-right">Actions</th>@endif</tr></thead>
			<tbody class="divide-y divide-border">
				@forelse ($records as $record)
					<tr class="transition hover:bg-background/70">
						<td class="px-5 py-4"><div class="font-medium text-primary">{{ ($record->user ?? $record->assignee)?->name }}</div><div class="mt-0.5 text-xs text-gray-400">Team member</div></td>
						<td class="px-5 py-3">
							@if ($slug === 'tasks')
								<div class="font-semibold text-primary">{{ $record->title }}</div>
								<div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
									<span class="rounded-full px-2.5 py-1 font-semibold {{ $record->priority === 'high' ? 'bg-error/10 text-error' : ($record->priority === 'low' ? 'bg-gray-100 text-gray-600' : 'bg-accent/10 text-accent') }}">{{ ucfirst($record->priority ?? 'normal') }} priority</span>
									<span>Due {{ $record->due_date?->format('d M Y') ?: 'No due date' }}</span>
								</div>
								@if ($record->description)<p class="mt-2 max-w-xl text-xs text-gray-500">{{ $record->description }}</p>@endif
			@elseif ($slug === 'scheduling')
				<div class="font-semibold text-primary">{{ $record->schedule_date?->format('d M Y') }}</div>
				<div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
					<span class="rounded-full bg-accent/10 px-2.5 py-1 font-semibold text-accent">{{ $record->starts_at ? \Illuminate\Support\Carbon::parse($record->starts_at)->format('g:i A') : 'Time in not set' }}</span>
					<span>to</span>
					<span class="rounded-full bg-secondary/10 px-2.5 py-1 font-semibold text-secondary">{{ $record->ends_at ? \Illuminate\Support\Carbon::parse($record->ends_at)->format('g:i A') : 'Time out not set' }}</span>
				</div>
				@if ($record->location)<p class="mt-2 text-xs text-gray-500">Location: {{ $record->location }}</p>@endif
							@elseif ($slug === 'performance')
								<div class="font-semibold text-primary">Performance · {{ $record->period }}</div>
								<div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
									<span class="rounded-full bg-success/10 px-2.5 py-1 font-semibold text-success">Score {{ number_format($record->score, 0) }}%</span>
									<span>{{ number_format($record->bookings_completed) }} bookings completed</span>
									<span>₱{{ number_format($record->revenue_generated, 2) }} revenue</span>
								</div>
								@if ($record->notes)<p class="mt-2 max-w-xl text-xs text-gray-500">{{ $record->notes }}</p>@endif
							@else
								{{ $record->period }} · PHP {{ number_format($record->amount, 2) }}
							@endif
						</td>
						<td class="px-5 py-4"><span class="rounded-full bg-accent/10 px-2.5 py-1 text-xs font-semibold capitalize text-accent">{{ $record->status ?? 'recorded' }}</span></td>
						<td class="px-5 py-4 text-gray-500">{{ $record->created_at->diffForHumans() }}</td>
						@if (in_array($slug, ['tasks', 'scheduling', 'performance']))
							<td class="px-5 py-4 text-right">
								@if (in_array($slug, ['tasks', 'scheduling']))
								<details class="relative inline-block text-left">
									<summary class="cursor-pointer list-none rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white transition hover:bg-secondary">Edit</summary>
									<div class="mt-2 ml-auto w-72 max-w-[calc(100vw-3rem)] rounded-xl border border-border bg-card p-4 text-left shadow-xl">
										<form method="POST" action="{{ route($slug . '.update', $record) }}" class="space-y-3">
											@csrf @method('PUT')
											@if ($slug === 'tasks')
												<input name="title" required value="{{ $record->title }}" placeholder="Task title" class="w-full rounded-lg border border-border px-3 py-2 text-sm">
												<select name="assigned_to" required class="w-full rounded-lg border border-border px-3 py-2 text-sm">@foreach ($users as $user)<option value="{{ $user->id }}" @selected($record->assigned_to == $user->id)>{{ $user->name }}</option>@endforeach</select>
												<select name="priority" class="w-full rounded-lg border border-border px-3 py-2 text-sm"><option value="low" @selected($record->priority === 'low')>Low</option><option value="normal" @selected($record->priority === 'normal')>Normal</option><option value="high" @selected($record->priority === 'high')>High</option></select>
												<input type="date" name="due_date" value="{{ $record->due_date?->format('Y-m-d') }}" class="w-full rounded-lg border border-border px-3 py-2 text-sm">
												<select name="status" class="w-full rounded-lg border border-border px-3 py-2 text-sm"><option value="todo" @selected($record->status === 'todo')>To do</option><option value="in_progress" @selected($record->status === 'in_progress')>In progress</option><option value="done" @selected($record->status === 'done')>Done</option></select>
											@else
												<select name="user_id" required class="w-full rounded-lg border border-border px-3 py-2 text-sm">@foreach ($users as $user)<option value="{{ $user->id }}" @selected($record->user_id == $user->id)>{{ $user->name }}</option>@endforeach</select>
												<input type="date" name="schedule_date" required value="{{ $record->schedule_date?->format('Y-m-d') }}" class="w-full rounded-lg border border-border px-3 py-2 text-sm">
												<input type="time" name="starts_at" value="{{ $record->starts_at ? substr((string) $record->starts_at, 0, 5) : '' }}" class="w-full rounded-lg border border-border px-3 py-2 text-sm">
												<input type="time" name="ends_at" value="{{ $record->ends_at ? substr((string) $record->ends_at, 0, 5) : '' }}" class="w-full rounded-lg border border-border px-3 py-2 text-sm">
												<input name="location" value="{{ $record->location }}" placeholder="Location" class="w-full rounded-lg border border-border px-3 py-2 text-sm">
												<input type="hidden" name="notes" value="{{ $record->notes }}">
											@endif
											<button class="w-full rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-secondary">Save changes</button>
										</form>
									</div>
								</details>
								@endif
								@if ($slug === 'performance')
									<form method="POST" action="{{ route($slug . '.destroy', $record) }}" class="mt-2" onsubmit="return confirm('Remove this {{ $slug === 'performance' ? 'performance record' : 'task' }}? This action cannot be undone.')">
										@csrf @method('DELETE')
										<button type="submit" class="rounded-lg border border-error/20 px-3 py-1.5 text-xs font-semibold text-error transition hover:border-error/40 hover:bg-error/5">Remove</button>
									</form>
								@endif
							</td>
						@endif
					</tr>
				@empty
					<tr><td colspan="{{ in_array($slug, ['tasks', 'scheduling', 'performance']) ? 5 : 4 }}" class="px-5 py-10 text-center text-gray-400">No records yet.</td></tr>
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
