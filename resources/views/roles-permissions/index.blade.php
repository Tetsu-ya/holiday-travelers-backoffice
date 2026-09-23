@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('content')
<div class="space-y-6">
	<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Access control</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Roles & permissions</h2><p class="mt-2 text-sm text-gray-500">Configure the access matrix used by your staff roles.</p></div><div class="rounded-xl border border-accent/20 bg-accent/5 px-3 py-2 text-xs font-medium text-accent">{{ count($permissions) }} permissions</div></div>
	<div class="overflow-x-auto rounded-2xl border border-border bg-card shadow-sm">
		<form method="POST" action="{{ route('roles-permissions.update') }}">
			@csrf
			<div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent p-5">
				<label class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Role to update</label>
				<select name="role" onchange="window.location='{{ route('roles-permissions.index') }}?role='+this.value" class="rounded-xl border border-border bg-card px-3 py-2.5 text-sm font-medium focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
					@foreach($roles as $role)
						<option value="{{ $role }}" {{ $selectedRole === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
					@endforeach
				</select>
			</div>
			<table class="w-full text-left text-sm">
				<thead class="bg-background text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
					<tr>
						<th class="px-5 py-3">Permission</th>
						@foreach($roles as $role)
							<th class="px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize {{ match ($role) { 'admin' => 'bg-secondary/10 text-secondary', 'manager' => 'bg-accent/10 text-accent', 'agent' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' } }}">{{ $role }}</span></th>
						@endforeach
					</tr>
				</thead>
				<tbody class="divide-y divide-border">
					@foreach($permissions as $key => $permission)
						<tr class="transition hover:bg-background/70">
							<td class="px-5 py-4 font-medium text-primary">{{ $permission }}</td>
							@foreach($roles as $role)
								<td class="px-5 py-3">
									<span class="{{ in_array($key, $rolePermissions[$role]) ? 'text-success' : 'text-gray-400' }}">
										{{ in_array($key, $rolePermissions[$role]) ? 'Enabled' : 'Restricted' }}
									</span>
								</td>
							@endforeach
						</tr>
					@endforeach
				</tbody>
			</table>
			<div class="border-t border-border bg-background/50 p-5">
				<p class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">Select permissions for the chosen role</p>
				<div class="grid gap-3 sm:grid-cols-2">
					@foreach($permissions as $key => $permission)
						<label class="flex items-center gap-3 rounded-xl border border-border bg-card p-3 text-sm transition hover:border-accent/40 hover:bg-accent/5">
							<input type="checkbox" name="permissions[{{ $key }}]" value="1" @checked(in_array($key, $rolePermissions[$selectedRole]))>
							<span>{{ $permission }}</span>
						</label>
					@endforeach
				</div>
				<button class="mt-5 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:-translate-y-0.5 hover:bg-secondary">Save permissions</button>
			</div>
		</form>
	</div>
</div>
@endsection
