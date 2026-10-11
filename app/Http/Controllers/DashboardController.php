<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\StaffAssignment;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Support\Revenue;

class DashboardController extends Controller
{
    public function index()
    {
        $year = (int) now()->year;
        $month = (int) now()->month;

        $kpis = [
            'tour_packages' => TourPackage::count(),
            'assigned_staff' => StaffAssignment::query()
                ->whereIn('status', ['assigned', 'confirmed'])
                ->whereHas('tourSchedule', fn ($query) => $query
                    ->whereDate('schedule_date', '>=', today())
                    ->whereIn('status', ['planned', 'confirmed']))
                ->distinct()
                ->count('user_id'),
            'packages_sold' => TourPackage::whereHas('bookings', fn ($query) => $query
                ->whereIn('status', Revenue::COMMITTED))
                ->count(),
            'revenue_this_month' => Revenue::collectedForMonth($month, $year),
        ];

        $upcomingTours = TourSchedule::with(['tourPackage', 'assignments.user'])
            ->whereDate('schedule_date', '>=', today())
            ->whereIn('status', ['planned', 'confirmed'])
            ->orderBy('schedule_date')
            ->limit(5)
            ->get();
        $assignedStaff = $upcomingTours
            ->flatMap(fn (TourSchedule $schedule) => $schedule->assignments
                ->whereIn('status', ['assigned', 'confirmed'])
                ->map(fn ($assignment) => $assignment->setRelation('tourSchedule', $schedule)))
            ->take(4);
        $packages = TourPackage::withCount([
            'bookings as sold_bookings' => fn ($query) => $query
                ->whereIn('status', Revenue::COMMITTED),
        ])
            ->orderByDesc('sold_bookings')
            ->latest()
            ->limit(4)
            ->get();
        $recentBookings = Booking::with('tourPackage')->latest()->limit(5)->get();

        return view('dashboard.index', compact(
            'kpis',
            'upcomingTours',
            'assignedStaff',
            'packages',
            'recentBookings',
        ));
    }
}
