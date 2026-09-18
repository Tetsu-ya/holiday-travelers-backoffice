@extends('layouts.app')
@section('title', 'New Booking')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-5">
        @csrf
        @include('bookings.partials.form')
        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Create booking</button>
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
