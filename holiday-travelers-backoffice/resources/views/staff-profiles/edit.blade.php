@extends('layouts.app')
@section('title', 'Edit Staff Profile')
@section('content')
<div class="max-w-4xl overflow-hidden rounded-2xl border border-border bg-card shadow-sm"><div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-6 py-5"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-secondary">People operations</p><h2 class="mt-1 font-heading text-xl font-semibold text-primary">Edit staff profile</h2><p class="mt-1 text-sm text-gray-500">Keep this team member’s details and access current.</p></div><form method="POST" action="{{ route('staff-profiles.update', $user) }}" class="space-y-6 p-6">@method('PUT')@include('staff-profiles._form', ['user' => $user])</form></div>
@endsection
