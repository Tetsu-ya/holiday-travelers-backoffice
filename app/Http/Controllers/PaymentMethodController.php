<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'paid_month' => 'nullable|integer|min:1|max:12',
            'paid_year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $query = Payment::with('booking')->latest('paid_at')->latest();

        if (!empty($filters['paid_month'])) {
            $query->whereYear('paid_at', $filters['paid_year'] ?? now()->year)
                ->whereMonth('paid_at', $filters['paid_month']);
        }

        $payments = $query->paginate(15)->withQueryString();
        $bookings = Booking::with('tourPackage')->latest()->take(15)->get();

        $summary = [
            'total' => $payments->sum('amount'),
            'paid' => (clone $query)->where('status', 'paid')->sum('amount'),
            'partial' => (clone $query)->where('status', 'partial')->sum('amount'),
            'refunded' => (clone $query)->where('status', 'refunded')->sum('amount'),
            'bookings' => Booking::count(),
            'filtered' => !empty($filters['paid_month']),
        ];

        return view('payment-methods.index', compact('payments', 'bookings', 'summary', 'filters'));
    }
}
