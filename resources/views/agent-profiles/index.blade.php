@extends('layouts.app')
@section('title', 'Agent Profiles')
@section('content')
<div class="space-y-6">
	<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
		<div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">People operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Agent profiles</h2><p class="mt-2 text-sm text-gray-500">Travel agents with booking and customer-facing responsibilities.</p></div>
		<div class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">{{ $agents->total() }} agents</div>
	</div>
	<div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
		<form method="GET" class="flex flex-col gap-3 border-b border-border bg-background/50 p-4 sm:flex-row"><input type="search" name="search" value="{{ $search }}" placeholder="Search agents" class="min-w-0 flex-1 rounded-xl border border-border bg-card px-3 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"><button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">Search</button></form>
		<div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500"><tr><th class="px-5 py-3.5">Agent</th><th class="px-5 py-3.5">Department</th><th class="px-5 py-3.5">Job title</th><th class="px-5 py-3.5">Status</th><th class="px-5 py-3.5">Action</th></tr></thead><tbody class="divide-y divide-border">@forelse($agents as $agent)<tr class="transition hover:bg-background/70"><td class="px-5 py-4"><div class="font-medium text-primary">{{ $agent->name }}</div><div class="mt-0.5 text-xs text-gray-500">{{ $agent->email }}</div></td><td class="px-5 py-4">{{ $agent->department ?: '—' }}</td><td class="px-5 py-4">{{ $agent->job_title ?: '—' }}</td><td class="px-5 py-4"><span class="rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold capitalize text-success">{{ $agent->status ?? 'active' }}</span></td><td class="px-5 py-4"><a href="{{ route('staff-profiles.show', $agent) }}" class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-primary transition hover:border-secondary hover:text-secondary">View profile</a></td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No agent profiles found. Add a staff profile with the Agent role first.</td></tr>@endforelse</tbody></table></div><div class="border-t border-border px-5 py-4">{{ $agents->links() }}</div>
	</div>
</div>
@endsection
