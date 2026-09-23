<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\DiscountCode;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PartnerCommission;
use App\Models\TourPackage;
use App\Support\Revenue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'travel_month' => 'nullable|integer|min:1|max:12',
            'travel_year' => 'nullable|integer|min:2000|max:2100',
            'paid_month' => 'nullable|integer|min:1|max:12',
            'paid_year' => 'nullable|integer|min:2000|max:2100',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed,refunded',
        ]);

        $query = Booking::with(['tourPackage', 'businessPartner']);

        if (!empty($filters['travel_month'])) {
            $query->whereYear('travel_date', $filters['travel_year'] ?? now()->year)
                ->whereMonth('travel_date', $filters['travel_month']);
        }

        if (!empty($filters['paid_month'])) {
            $paidYear = (int) ($filters['paid_year'] ?? now()->year);
            $paidMonth = (int) $filters['paid_month'];
            $query->whereHas('payments', function ($payment) use ($paidMonth, $paidYear) {
                $payment->whereIn('status', Revenue::RECEIVED)
                    ->whereYear('paid_at', $paidYear)
                    ->whereMonth('paid_at', $paidMonth);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();
        $bookingStats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'refunded' => Booking::where('status', 'refunded')->count(),
        ];

        return view('bookings.index', compact('bookings', 'bookingStats', 'filters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'business_partner_id' => 'nullable|exists:business_partners,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string|max:50',
            'pax' => 'required|integer|min:1',
            'travel_date' => 'required|date',
            'travel_time' => 'nullable|date_format:H:i',
            'total_amount' => 'required|numeric|min:0',
            'discount_code' => 'nullable|string|max:50',
        ]);

        $discountCode = null;
        $subtotal = (float) $data['total_amount'];
        $discountAmount = 0;

        $booking = app(DatabaseManager::class)->transaction(function () use (&$discountCode, &$discountAmount, $data, $subtotal) {
            if (!empty($data['discount_code'])) {
                $discountCode = DiscountCode::whereRaw('UPPER(code) = ?', [strtoupper(trim($data['discount_code']))])
                    ->where('status', 'active')
                    ->whereDate('starts_on', '<=', today())
                    ->whereDate('ends_on', '>=', today())
                    ->lockForUpdate()
                    ->first();

                if (!$discountCode) {
                    throw ValidationException::withMessages(['discount_code' => 'This discount code is invalid, inactive, or outside its validity dates.']);
                }

                if ($discountCode->usage_limit !== null && $discountCode->used_count >= $discountCode->usage_limit) {
                    throw ValidationException::withMessages(['discount_code' => 'This discount code has reached its usage limit.']);
                }

                $discountAmount = $discountCode->discount_type === 'percentage'
                    ? min($subtotal, $subtotal * ((float) $discountCode->discount_value / 100))
                    : min($subtotal, (float) $discountCode->discount_value);
            }

            $bookingData = collect($data)->except('discount_code')->all();
            $bookingData['reference_no'] = 'HTI-' . strtoupper(uniqid());
            $bookingData['subtotal_amount'] = $subtotal;
            $bookingData['discount_code_id'] = $discountCode?->id;
            $bookingData['discount_amount'] = round($discountAmount, 2);
            $bookingData['total_amount'] = round($subtotal - $discountAmount, 2);

            $booking = Booking::create($bookingData);
            $discountCode?->increment('used_count');
            $this->syncPartnerCommission($booking);

            return $booking;
        });

        return redirect()->route('bookings.index')->with('success', 'Booking created.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['tourPackage', 'businessPartner', 'discountCode']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        return view('bookings.edit', [
            'booking' => $booking,
            'packages' => TourPackage::orderBy('name')->get(),
            'partners' => BusinessPartner::orderBy('name')->get(),
            'discountCodes' => DiscountCode::where('status', 'active')->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'business_partner_id' => 'nullable|exists:business_partners,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'pax' => 'required|integer|min:1',
            'travel_date' => 'required|date',
            'travel_time' => 'nullable|date_format:H:i',
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded,cancelled',
            'status' => 'required|in:pending,confirmed,cancelled,completed,refunded',
        ]);

        $booking->update($data);
        $this->syncPartnerCommission($booking->fresh());
        return redirect()->route('bookings.index')->with('success', 'Booking updated.');
    }

    public function finance(Booking $booking)
    {
        $booking->load([
            'tourPackage',
            'businessPartner',
            'payments',
            'invoices',
            'statusHistories.changedBy',
            'partnerCommissions.partner',
            'documents',
        ]);

        return view('bookings.finance', compact('booking'));
    }

    public function receipt(Payment $payment)
    {
        $payment->load('booking.tourPackage');

        return view('bookings.receipt', compact('payment'));
    }

    public function recordPayment(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,bank_transfer,wallet,other',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,paid,partial,refunded,cancelled',
        ]);

        $data['booking_id'] = $booking->id;
        $data['paid_at'] = in_array($data['status'], ['paid', 'partial'], true) ? now() : null;
        Payment::create($data);

        $this->syncBookingStatus($booking);

        return redirect()->route('bookings.finance', $booking)->with('success', 'Payment recorded successfully.');
    }

    public function destroyPayment(Payment $payment)
    {
        $booking = $payment->booking;

        $payment->delete();

        if ($booking) {
            $this->syncBookingStatus($booking);

            return redirect()->route('bookings.finance', $booking)->with('success', 'Payment removed.');
        }

        return redirect()->route('payment-methods.index')->with('success', 'Payment removed.');
    }

    public function generateInvoice(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'status' => 'required|in:draft,issued,paid,overdue',
            'issued_at' => 'nullable|date',
            'due_at' => 'nullable|date',
        ]);

        $data['booking_id'] = $booking->id;
        $data['total'] = (float) $data['subtotal'] + (float) $data['tax'];

        Invoice::updateOrCreate(
            ['booking_id' => $booking->id],
            $data
        );

        return redirect()->route('bookings.finance', $booking)->with('success', 'Invoice saved successfully.');
    }

    /**
     * Recalculate and persist a booking's payment status and booking status
     * from its current payment rows.
     *
     * A refunded or cancelled payment must be reflected on the booking itself:
     *  - every received payment refunded   -> booking payment_status = refunded
     *  - every payment cancelled (no cash) -> booking payment_status = cancelled
     *  - a refunded/cancelled payment on an otherwise paid booking keeps the
     *    booking status in step (cancelled / refunded) instead of leaving it
     *    confirmed.
     */
    protected function syncBookingStatus(Booking $booking): void
    {
        $booking->refresh();

        $receivedTotal = (float) $booking->payments()->whereIn('status', ['paid', 'partial'])->sum('amount');
        $refundedTotal = (float) $booking->payments()->where('status', 'refunded')->sum('amount');
        $cancelledTotal = (float) $booking->payments()->where('status', 'cancelled')->sum('amount');

        $booking->payment_status = $this->resolvePaymentStatus($booking, $receivedTotal, $refundedTotal, $cancelledTotal);

        // Keep the booking status aligned with refunded/cancelled money.
        if ($booking->payment_status === 'refunded' || $booking->payment_status === 'cancelled') {
            $booking->status = $booking->payment_status;
        } elseif ($receivedTotal > 0) {
            $booking->status = 'confirmed';
        }

        $booking->save();
    }

    protected function resolvePaymentStatus(
        Booking $booking,
        ?float $receivedTotal = null,
        ?float $refundedTotal = null,
        ?float $cancelledTotal = null
    ): string {
        $receivedTotal ??= (float) $booking->payments()->whereIn('status', ['paid', 'partial'])->sum('amount');
        $refundedTotal ??= (float) $booking->payments()->where('status', 'refunded')->sum('amount');
        $cancelledTotal ??= (float) $booking->payments()->where('status', 'cancelled')->sum('amount');

        if ($receivedTotal > 0) {
            if ($receivedTotal >= (float) $booking->total_amount) {
                return 'paid';
            }

            return 'partial';
        }

        // No money is currently held: surface the strongest refund/cancel signal.
        if ($refundedTotal > 0) {
            return 'refunded';
        }

        if ($cancelledTotal > 0) {
            return 'cancelled';
        }

        return 'unpaid';
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted.');
    }

    public function create()
    {
        return view('bookings.create', [
            'packages' => TourPackage::orderBy('name')->get(),
            'partners' => BusinessPartner::orderBy('name')->get(),
            'discountCodes' => DiscountCode::where('status', 'active')->orderBy('code')->get(),
        ]);
    }

    protected function syncPartnerCommission(Booking $booking): void
    {
        $existing = PartnerCommission::where('booking_id', $booking->id)->first();
        $rate = (float) ($booking->businessPartner?->commission_rate ?? 0);

        if (!$booking->business_partner_id || $rate <= 0) {
            PartnerCommission::where('booking_id', $booking->id)
                ->whereIn('status', ['pending', 'approved'])
                ->delete();
            return;
        }

        $commission = $existing ?: new PartnerCommission(['status' => 'pending']);
        $commission->booking_id = $booking->id;
        $commission->partner_id = $booking->business_partner_id;
        $commission->commission_rate = $rate;
        $commission->commission_amount = round((float) $booking->total_amount * ($rate / 100), 2);
        $commission->save();
    }
}
