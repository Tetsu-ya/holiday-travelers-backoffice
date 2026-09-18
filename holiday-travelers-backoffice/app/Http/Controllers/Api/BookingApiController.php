<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingApiController extends Controller
{
    // GET /api/bookings
    public function index()
    {
        return Booking::with(['tourPackage', 'businessPartner'])->latest()->paginate(20);
    }

    // POST /api/bookings
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
        $booking = Booking::create($data);

        return response()->json($booking, 201);
    }

    // GET /api/bookings/{booking}
    public function show(Booking $booking)
    {
        return $booking->load(['tourPackage', 'businessPartner']);
    }
}
