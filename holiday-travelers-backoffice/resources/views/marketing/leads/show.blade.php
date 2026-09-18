@extends('layouts.app')
@section('title', $lead->name)

@section('content')
<div class="max-w-3xl space-y-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Lead</p>
            <h1 class="text-2xl font-semibold">{{ $lead->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('leads.index') }}" class="px-4 py-2 rounded-lg border border-border bg-white">Back</a>
            <a href="{{ route('leads.edit', $lead) }}" class="px-4 py-2 rounded-lg bg-primary text-white">Edit</a>
        </div>
    </div>

    <div class="bg-card rounded-xl border border-border shadow-sm p-6">
        <dl class="grid gap-5 md:grid-cols-2">
            <div><dt class="text-sm text-gray-500">Email</dt><dd>{{ $lead->email ?: 'N/A' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Phone</dt><dd>{{ $lead->phone ?: 'N/A' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Interested package</dt><dd>{{ $lead->interested_package ?: 'N/A' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Status</dt><dd class="capitalize">{{ $lead->status }}</dd></div>
            <div><dt class="text-sm text-gray-500">Campaign</dt><dd>{{ $lead->campaign?->name ?? 'General lead' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
