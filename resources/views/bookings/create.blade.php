@extends('layouts.app')
@section('title', 'New Booking')

@section('content')
<div class="max-w-4xl overflow-hidden rounded-2xl border border-border bg-card shadow-xl shadow-primary/5">
    <div class="border-b border-border bg-gradient-to-r from-primary/[0.05] to-transparent px-6 py-6 sm:px-8"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Travel operations</p><h2 class="mt-1 font-heading text-2xl font-semibold tracking-tight text-primary">Create new booking</h2><p class="mt-2 text-sm text-gray-500">Capture the customer, package, travel date, and payment details.</p></div>
    @if ($errors->any())<div class="mx-6 mt-6 rounded-xl border border-error/20 bg-error/5 p-4 text-sm text-error sm:mx-8"><p class="font-semibold">Please check the booking details.</p><ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-6 p-6 sm:p-8">
        @csrf
        @include('bookings.partials.form')
        <div class="flex flex-wrap gap-3 pt-2">
            <button type="submit" class="group inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition-all duration-200 hover:-translate-y-0.5 hover:bg-secondary hover:shadow-xl active:translate-y-0 active:scale-95"><span>Create booking</span><span class="transition-transform duration-200 group-hover:translate-x-1">→</span></button>
            <a href="{{ route('bookings.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-border px-5 py-2.5 text-sm font-semibold text-primary transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary active:translate-y-0 active:scale-95"><span class="transition-transform duration-200 group-hover:-translate-x-1">←</span><span>Cancel</span></a>
        </div>
    </form>
</div>
@endsection
