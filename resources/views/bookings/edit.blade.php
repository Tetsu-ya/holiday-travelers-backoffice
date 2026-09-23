@extends('layouts.app')
@section('title', 'Edit Booking')

@section('content')
<div class="max-w-3xl bg-card rounded-xl border border-border shadow-sm p-6">
    <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-5">
        @csrf
        @method('PUT')
        @include('bookings.partials.form')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="status" class="block text-sm font-medium mb-1">Booking status</label>
                <select id="status" name="status" class="w-full rounded-lg border-border" required>
                    @foreach (['pending', 'confirmed', 'cancelled', 'completed', 'refunded'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $booking->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="payment_status" class="block text-sm font-medium mb-1">Payment status</label>
                <select id="payment_status" name="payment_status" class="w-full rounded-lg border-border" required>
                    @foreach (['unpaid', 'partial', 'paid', 'refunded', 'cancelled'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected(old('payment_status', $booking->payment_status) === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                    @endforeach
                </select>
                @error('payment_status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white">Save changes</button>
            <a href="{{ route('bookings.show', $booking) }}" class="px-4 py-2 rounded-lg border border-border">Cancel</a>
        </div>
    </form>
</div>
@endsection
