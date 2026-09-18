<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\MarketingCampaign;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'total_bookings' => Booking::count(),
            'revenue_this_month' => Booking::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'active_partners' => BusinessPartner::where('status', 'active')->count(),
            'active_suppliers' => Supplier::where('status', 'active')->count(),
            'running_campaigns' => MarketingCampaign::where('status', 'active')->count(),
        ];

        $recentBookings = Booking::with('tourPackage')->latest()->limit(5)->get();

        return view('dashboard.index', compact('kpis', 'recentBookings'));
    }
}
