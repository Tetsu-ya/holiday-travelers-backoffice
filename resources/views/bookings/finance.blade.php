@extends('layouts.app')
@section('title', 'Booking Finance - ' . $booking->reference_no)

@section('content')
<div class="max-w-6xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">Booking finance</p>
            <h1 class="text-2xl font-semibold">{{ $booking->reference_no }}</h1>
        </div>
        <a href="{{ route('payment-methods.index') }}" class="group inline-flex items-center gap-2 rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-semibold text-primary transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary active:translate-y-0 active:scale-95"><span class="transition-transform duration-200 group-hover:-translate-x-1">←</span><span>Back</span></a>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="bg-card rounded-xl border border-border shadow-sm p-6">
            <h2 class="mb-4 text-lg font-semibold">Record payment</h2>
            <form method="POST" action="{{ route('bookings.finance.payment', $booking) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Amount</label>
                    <input name="amount" type="number" step="0.01" min="0" required class="w-full rounded-lg border border-border px-3 py-2" placeholder="0.00">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Payment method</label>
                    <select name="payment_method" class="w-full rounded-lg border border-border px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank transfer</option>
                        <option value="wallet">Wallet</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Reference</label>
                    <input name="reference_number" type="text" class="w-full rounded-lg border border-border px-3 py-2" placeholder="Receipt / transfer ref">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full rounded-lg border border-border px-3 py-2">
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="pending">Pending</option>
                        <option value="refunded">Refunded</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition-all duration-200 hover:-translate-y-0.5 hover:bg-secondary hover:shadow-xl active:translate-y-0 active:scale-95"><span>Save payment</span><span class="transition-transform duration-200 group-hover:translate-x-1">→</span></button>
            </form>
        </div>

        <div class="bg-card rounded-xl border border-border shadow-sm p-6">
            <h2 class="mb-4 text-lg font-semibold">Create invoice</h2>
            <form method="POST" action="{{ route('bookings.finance.invoice', $booking) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Invoice number</label>
                    <input name="invoice_number" type="text" required class="w-full rounded-lg border border-border px-3 py-2" value="INV-{{ $booking->reference_no }}">
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Subtotal</label>
                        <input name="subtotal" type="number" step="0.01" min="0" required class="w-full rounded-lg border border-border px-3 py-2" value="{{ $booking->total_amount }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tax</label>
                        <input name="tax" type="number" step="0.01" min="0" required class="w-full rounded-lg border border-border px-3 py-2" value="0">
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Issue date</label>
                        <input name="issued_at" type="date" class="w-full rounded-lg border border-border px-3 py-2">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Due date</label>
                        <input name="due_at" type="date" class="w-full rounded-lg border border-border px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full rounded-lg border border-border px-3 py-2">
                        <option value="draft">Draft</option>
                        <option value="issued">Issued</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <button type="submit" class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-secondary/15 transition-all duration-200 hover:-translate-y-0.5 hover:bg-primary hover:shadow-xl active:translate-y-0 active:scale-95"><span>Generate invoice</span><span class="transition-transform duration-200 group-hover:translate-x-1">→</span></button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="bg-card rounded-xl border border-border shadow-sm p-6">
            <h2 class="mb-4 text-lg font-semibold">Recent payments</h2>
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
                                    <a href="{{ route('payments.receipt', $payment) }}" class="no-print mt-1 inline-block text-xs font-semibold text-secondary hover:text-primary">View receipt</a>
                                    <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="no-print mt-1" onsubmit="return confirm('Remove this payment of PHP {{ number_format((float) $payment->amount, 2) }}? The booking balance will be recalculated.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-error transition hover:text-red-700">Remove</button>
                                    </form>
                                </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-card rounded-xl border border-border shadow-sm p-6">
            <h2 class="mb-4 text-lg font-semibold">Invoice status</h2>
            @php $invoice = $booking->invoices->first(); @endphp
            @if($invoice)
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between"><span class="text-gray-500">Invoice number</span><span class="font-medium">{{ $invoice->invoice_number }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Issue date</span><span>{{ $invoice->issued_at?->format('M d, Y') ?? 'Not set' }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Due date</span><span>{{ $invoice->due_at?->format('M d, Y') ?? 'Not set' }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Subtotal</span><span>PHP {{ number_format((float) $invoice->subtotal, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Tax</span><span>PHP {{ number_format((float) $invoice->tax, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Total</span><span class="font-semibold">PHP {{ number_format((float) $invoice->total, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Status</span><span class="capitalize">{{ $invoice->status }}</span></div>
                </div>
            @else
                <p class="text-sm text-gray-500">No invoice created yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
