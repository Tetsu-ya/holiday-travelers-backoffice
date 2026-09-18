@extends('layouts.app')
@section('title', $module)

@section('content')
<div class="flex min-h-[420px] items-center justify-center">
    <div class="max-w-md text-center">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary/10 text-2xl text-secondary">+</div>
        <h2 class="font-heading text-2xl font-semibold text-primary">{{ $module }}</h2>
        <p class="mt-2 text-sm leading-6 text-gray-500">This workspace is connected to the back office navigation and is ready for its workflow to be added.</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-flex rounded-lg bg-secondary px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">Back to dashboard</a>
    </div>
</div>
@endsection