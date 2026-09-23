@extends('layouts.app')
@section('title', 'Booking ' . $booking->reference_no)

@section('content')
<div class="max-w-6xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Booking reference</p>
            <h1 class="text-2xl font-semibold">{{ $booking->reference_no }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 rounded-lg border border-border bg-white">Back</a>
            <a href="{{ route('bookings.finance', $booking) }}" class="px-4 py-2 rounded-lg bg-secondary text-white">Finance</a>
            <a href="{{ route('bookings.edit', $booking) }}" class="px-4 py-2 rounded-lg bg-primary text-white">Edit booking</a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
        <div class="space-y-6">
            <div class="bg-card rounded-xl border border-border shadow-sm p-6">
                <dl class="grid gap-5 md:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">Customer</dt><dd class="font-medium">{{ $booking->customer_name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Package</dt><dd class="font-medium">{{ $booking->tourPackage->name ?? 'Unavailable' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Email</dt><dd>{{ $booking->customer_email ?: 'Not provided' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Phone</dt><dd>{{ $booking->customer_phone ?: 'Not provided' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Passengers</dt><dd>{{ $booking->pax }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Travel date</dt><dd>{{ $booking->travel_date?->format('M d, Y') ?? 'Not set' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Travel time</dt><dd>{{ $booking->travel_time ? \Illuminate\Support\Carbon::parse($booking->travel_time)->format('g:i A') : 'Not set' }}</dd></div>
                    @if ($booking->discountCode)
                        <div><dt class="text-sm text-gray-500">Subtotal</dt><dd>PHP {{ number_format((float) ($booking->subtotal_amount ?? $booking->total_amount), 2) }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Discount</dt><dd class="font-medium text-success">{{ $booking->discountCode->code }} · PHP {{ number_format((float) $booking->discount_amount, 2) }}</dd></div>
                    @endif
                    <div><dt class="text-sm text-gray-500">Total amount</dt><dd>PHP {{ number_format((float) $booking->total_amount, 2) }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Business partner</dt><dd>{{ $booking->businessPartner->name ?? 'Direct booking' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Booking status</dt><dd class="capitalize">{{ $booking->status }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Payment status</dt><dd class="capitalize">{{ $booking->payment_status }}</dd></div>
                </dl>
            </div>

            <div class="bg-card rounded-xl border border-border shadow-sm p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Payment summary</h2>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">{{ $booking->payments->count() }} payment(s)</span>
                </div>

                @if($booking->payments->isEmpty())
                    <p class="text-sm text-gray-500">No payments recorded yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($booking->payments as $payment)
                            <div class="flex items-center justify-between rounded-lg border border-border bg-gray-50 p-3">
                                <div>
                                    <div class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->reference_number ?: 'No reference' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">PHP {{ number_format((float) $payment->amount, 2) }}</div>
                                    <div class="text-xs capitalize text-gray-500">{{ $payment->status }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-card rounded-xl border border-border shadow-sm p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Documents</h2>
                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">{{ $booking->documents->count() }} file(s)</span>
                </div>

                @if($booking->documents->isEmpty())
                    <p class="text-sm text-gray-500">No documents uploaded for this booking.</p>
                @else
                    <div class="space-y-3">
                        @foreach($booking->documents as $document)
                            <div class="flex items-center justify-between rounded-lg border border-border bg-gray-50 p-3">
                                <div>
                                    <div class="font-medium">{{ $document->file_name }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($document->document_type) }}</div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $document->created_at->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-card rounded-xl border border-border shadow-sm p-6">
                <h2 class="mb-4 text-lg font-semibold">Invoice</h2>
                @php $invoice = $booking->invoices->first(); @endphp
                @if($invoice)
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between"><span class="text-gray-500">Invoice no.</span><span class="font-medium">{{ $invoice->invoice_number }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Issue date</span><span>{{ $invoice->issued_at?->format('M d, Y') ?? 'Not set' }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Due date</span><span>{{ $invoice->due_at?->format('M d, Y') ?? 'Not set' }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Subtotal</span><span>PHP {{ number_format((float) $invoice->subtotal, 2) }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Tax</span><span>PHP {{ number_format((float) $invoice->tax, 2) }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Total</span><span class="font-semibold">PHP {{ number_format((float) $invoice->total, 2) }}</span></div>
                        <div class="flex items-center justify-between"><span class="text-gray-500">Status</span><span class="capitalize">{{ $invoice->status }}</span></div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No invoice generated yet.</p>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
