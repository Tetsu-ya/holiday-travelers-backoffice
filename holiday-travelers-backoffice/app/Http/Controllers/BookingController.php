<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['tourPackage', 'businessPartner'])->latest()->paginate(15);
        $bookingStats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('bookings.index', compact('bookings', 'bookingStats'));
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
            'total_amount' => 'required|numeric|min:0',
        ]);

        $data['reference_no'] = 'HTI-' . strtoupper(uniqid());
        Booking::create($data);
        return redirect()->route('bookings.index')->with('success', 'Booking created.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['tourPackage', 'businessPartner']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        return view('bookings.edit', [
            'booking' => $booking,
            'packages' => TourPackage::orderBy('name')->get(),
            'partners' => BusinessPartner::orderBy('name')->get(),
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
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:unpaid,partial,paid,refunded',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update($data);
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

    public function recordPayment(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,bank_transfer,wallet,other',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,paid,partial,refunded',
        ]);

        $data['booking_id'] = $booking->id;
        Payment::create($data);

        $booking->refresh();
        $booking->payment_status = $this->resolvePaymentStatus($booking);
        $booking->save();

        return redirect()->route('bookings.finance', $booking)->with('success', 'Payment recorded successfully.');
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

    protected function resolvePaymentStatus(Booking $booking): string
    {
        $paidTotal = (float) $booking->payments()->whereIn('status', ['paid', 'partial'])->sum('amount');

        if ($paidTotal >= (float) $booking->total_amount) {
            return 'paid';
        }

        if ($paidTotal > 0) {
            return 'partial';
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
        ]);
    }
}