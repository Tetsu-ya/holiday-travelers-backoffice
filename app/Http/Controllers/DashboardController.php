<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\MarketingCampaign;
use App\Models\Supplier;
use App\Support\Revenue;

class DashboardController extends Controller
{
    public function index()
    {
        $year = (int) now()->year;
        $month = (int) now()->month;

        $kpis = [
            'total_bookings' => Booking::count(),
            'revenue_this_month' => Revenue::collectedForMonth($month, $year),
            'booked_value' => Revenue::bookedValue(),
            'outstanding' => Revenue::outstanding(),
            'active_partners' => BusinessPartner::where('status', 'active')->count(),
            'active_suppliers' => Supplier::where('status', 'active')->count(),
            'running_campaigns' => MarketingCampaign::where('status', 'active')->count(),
        ];

        // Real bookings trend: bookings created per month for the current year.
        $monthlyBookings = collect(range(1, 12))->mapWithKeys(fn (int $m) => [
            $m => Booking::whereYear('created_at', $year)->whereMonth('created_at', $m)->count(),
        ]);

        // Top destination by number of bookings.
        $topDestination = Booking::query()
            ->join('tour_packages', 'tour_packages.id', '=', 'bookings.tour_package_id')
            ->whereNotNull('tour_packages.destination')
            ->selectRaw('tour_packages.destination as destination, COUNT(*) as total')
            ->groupBy('tour_packages.destination')
            ->orderByDesc('total')
            ->first();

        // Best channel by conversions across marketing campaigns.
        $bestChannel = MarketingCampaign::query()
            ->whereNotNull('channel')
            ->selectRaw('channel, SUM(conversions) as total')
            ->groupBy('channel')
            ->orderByDesc('total')
            ->first();

        $recentBookings = Booking::with('tourPackage')->latest()->limit(5)->get();

        return view('dashboard.index', compact(
            'kpis',
            'monthlyBookings',
            'topDestination',
            'bestChannel',
            'recentBookings',
        ));
    }
}
