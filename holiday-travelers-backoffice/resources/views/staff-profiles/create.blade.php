@extends('layouts.app')
@section('title', 'Add Staff Profile')
@section('content')
<div class="max-w-4xl overflow-hidden rounded-2xl border border-border bg-card shadow-sm"><div class="border-b border-border bg-gradient-to-r from-primary/[0.04] to-transparent px-6 py-5"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-secondary">People operations</p><h2 class="mt-1 font-heading text-xl font-semibold text-primary">Create staff profile</h2><p class="mt-1 text-sm text-gray-500">Add a team member to the back-office directory.</p></div><form method="POST" action="{{ route('staff-profiles.store') }}" class="space-y-6 p-6">@include('staff-profiles._form', ['user' => null])</form></div>
@endsection
