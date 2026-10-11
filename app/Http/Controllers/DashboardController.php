<?php

namespace App\Http\Controllers;

use App\Models\BusinessPartner;
use App\Models\StaffAssignment;
use App\Models\Supplier;
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

        $upcomingTours = TourSchedule::with(['tourPackage', 'assignments' => fn ($query) => $query
            ->with('user')
            ->whereIn('status', ['assigned', 'confirmed'])])
            ->whereDate('schedule_date', '>=', today())
            ->whereIn('status', ['planned', 'confirmed'])
            ->orderBy('schedule_date')
            ->limit(5)
            ->get();
        $assignedStaff = $upcomingTours
            ->flatMap(fn (TourSchedule $schedule) => $schedule->assignments
                ->map(fn (StaffAssignment $assignment) => $assignment->setRelation('tourSchedule', $schedule)))
            ->take(4);
        $packages = TourPackage::withCount([
            'bookings as sold_bookings' => fn ($query) => $query
                ->whereIn('status', Revenue::COMMITTED),
        ])
            ->orderByDesc('sold_bookings')
            ->latest()
            ->limit(4)
            ->get();
        $directoryEntries = Supplier::query()
            ->latest()
            ->limit(5)
            ->get()
            ->each(function (Supplier $supplier) {
                $supplier->setAttribute('directory_type', 'Supplier');
                $supplier->setAttribute('directory_category', str_replace('_', ' ', ucfirst($supplier->category)));
            })
            ->concat(BusinessPartner::query()
                ->latest()
                ->limit(5)
                ->get()
                ->each(function (BusinessPartner $partner) {
                    $partner->setAttribute('directory_type', 'Partner');
                    $partner->setAttribute('directory_category', str_replace('_', ' ', ucfirst($partner->type)));
                }))
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        return view('dashboard.index', compact(
            'kpis',
            'upcomingTours',
            'assignedStaff',
            'packages',
            'directoryEntries',
        ));
    }
}
