@extends('layouts.app')
@section('title', $partner->name)

@section('content')
<div class="max-w-3xl space-y-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Business partner</p>
            <h1 class="text-2xl font-semibold">{{ $partner->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('partners.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-semibold text-primary transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary active:translate-y-0 active:scale-95"><span class="transition-transform duration-200 group-hover:-translate-x-1">←</span><span>Back</span></a>
            <a href="{{ route('partners.edit', $partner) }}" class="group inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition-all duration-200 hover:-translate-y-0.5 hover:bg-secondary hover:shadow-xl active:translate-y-0 active:scale-95"><span>Edit</span><span class="transition-transform duration-200 group-hover:translate-x-1">→</span></a>
        </div>
    </div>

    <div class="bg-card rounded-xl border border-border shadow-sm p-6">
        <dl class="grid gap-5 md:grid-cols-2">
            <div><dt class="text-sm text-gray-500">Type</dt><dd class="font-medium capitalize">{{ $partner->type }}</dd></div>
            <div><dt class="text-sm text-gray-500">Region</dt><dd>{{ $partner->region ?: 'N/A' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Contact person</dt><dd>{{ $partner->contact_person ?: 'N/A' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Commission</dt><dd>{{ $partner->commission_rate ?? 0 }}%</dd></div>
            <div><dt class="text-sm text-gray-500">Email</dt><dd>{{ $partner->email ?: 'N/A' }}</dd></div>
            <div><dt class="text-sm text-gray-500">Phone</dt><dd>{{ $partner->phone ?: 'N/A' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
