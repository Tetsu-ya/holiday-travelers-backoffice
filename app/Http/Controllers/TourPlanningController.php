<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ResourceAllocation;
use App\Models\StaffAssignment;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Models\User;
use App\Services\TravelReadinessService;
use Illuminate\Http\Request;

class TourPlanningController extends Controller
{
    public function schedule()
    {
        $schedules = TourSchedule::with('tourPackage')->orderBy('schedule_date')->paginate(15);
        return view('tour-planning.index', ['module' => 'schedule', 'schedules' => $schedules, 'packages' => TourPackage::orderBy('name')->get(), 'stats' => $this->overviewStats()]);
    }

    public function storeSchedule(Request $request, TravelReadinessService $readiness)
    {
        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id', 'schedule_date' => 'required|date',
            'starts_at' => 'nullable|date_format:H:i', 'ends_at' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255', 'destination' => 'nullable|string|max:255', 'traffic_origin' => 'nullable|string|max:255', 'capacity' => 'required|integer|min:1',
            'status' => 'required|in:planned,confirmed,cancelled,completed', 'notes' => 'nullable|string',
        ]);
        $schedule = TourSchedule::create($data);

        if (in_array($schedule->status, ['planned', 'confirmed'], true) && $schedule->schedule_date->gte(today())) {
            return redirect()->route('ai-planning.index')
                ->with('success', 'Tour schedule added. Travel readiness was checked automatically.')
                ->with('travel_readiness', $readiness->assess($schedule->load('tourPackage')));
        }

        return back()->with('success', 'Tour schedule added.');
    }

    public function updateTrafficOrigin(Request $request, TourSchedule $schedule, TravelReadinessService $readiness)
    {
        $schedule->update($request->validate([
            'traffic_origin' => 'nullable|string|max:255',
        ]));

        if (in_array($schedule->status, ['planned', 'confirmed'], true) && $schedule->schedule_date->gte(today())) {
            return redirect()->route('ai-planning.index')
                ->with('success', 'Traffic route origin updated. Travel readiness was checked again.')
                ->with('travel_readiness', $readiness->assess($schedule->load('tourPackage')));
        }

        return back()->with('success', 'Traffic route origin updated.');
    }

    public function availability()
    {
        $packages = TourPackage::withSum(['bookings as booked_pax' => fn ($query) => $query->whereIn('status', ['pending', 'confirmed'])], 'pax')->latest()->paginate(15);
        return view('tour-planning.index', ['module' => 'availability', 'packages' => $packages, 'stats' => $this->overviewStats()]);
    }

    private function overviewStats(): array
    {
        $confirmed = Booking::whereIn('status', ['pending', 'confirmed'])->sum('pax');
        return [
            'packages' => TourPackage::count(),
            'upcoming' => TourSchedule::whereDate('schedule_date', '>=', today())->count(),
            'confirmed' => $confirmed,
            'open_slots' => max(0, TourPackage::sum('slots') - $confirmed),
            'staff' => [
                'total' => User::count(),
                'guides' => User::where('role', 'tour_guide')->count(),
                'drivers' => User::where('role', 'driver')->count(),
                'agents' => User::whereIn('role', ['agent', 'travel_agent'])->count(),
                'others' => User::whereNotIn('role', ['tour_guide', 'driver', 'agent', 'travel_agent'])->count(),
            ],
        ];
    }

    public function allocation()
    {
        $schedules = TourSchedule::with(['tourPackage', 'assignments.user', 'allocations'])
            ->whereDate('schedule_date', '>=', today())
            ->orderBy('schedule_date')->paginate(15);

        $schedules->getCollection()->each(function ($schedule) {
            $schedule->assignments->each(function ($assignment) {
                if ($assignment->user) {
                    $assignment->user->setAttribute('name', $assignment->user->name . ' · ' . str_replace('_', ' ', ucfirst($assignment->assignment_role)));
                }
            });
        });

        return view('tour-planning.resource-allocation', [
            'allocations' => ResourceAllocation::with('tourSchedule.tourPackage')->latest()->paginate(15),
            'scheduleOptions' => TourSchedule::with('tourPackage')->whereDate('schedule_date', '>=', today())->orderBy('schedule_date')->get(),
            'schedules' => $schedules,
            'packages' => TourPackage::withSum(['bookings as booked_pax' => fn ($query) => $query->whereIn('status', ['pending', 'confirmed'])], 'pax')->orderBy('name')->get(),
            'stats' => $this->overviewStats(),
        ]);
    }

    public function storeAllocation(Request $request)
    {
        ResourceAllocation::create($request->validate(['tour_schedule_id' => 'required|exists:tour_schedules,id', 'resource_type' => 'required|string|max:100', 'resource_name' => 'required|string|max:255', 'quantity' => 'required|integer|min:1', 'status' => 'required|in:reserved,requested,confirmed', 'notes' => 'nullable|string']));
        return back()->with('success', 'Resource allocation added.');
    }

    public function assignment(Request $request)
    {
        $selectedPackageId = $request->integer('package_id');
        $assignments = StaffAssignment::with(['tourSchedule.tourPackage', 'user'])
            ->when($selectedPackageId, fn ($query) => $query->whereHas('tourSchedule', fn ($schedule) => $schedule->where('tour_package_id', $selectedPackageId)))
            ->latest()->paginate(15)->withQueryString();
        $schedules = TourSchedule::with('tourPackage')
            ->when($selectedPackageId, fn ($query) => $query->where('tour_package_id', $selectedPackageId))
            ->orderBy('schedule_date')->get();
        return view('tour-planning.index', ['module' => 'assignment', 'assignments' => $assignments, 'schedules' => $schedules, 'packages' => TourPackage::with('schedules')->where('status', 'published')->orderBy('name')->get(), 'selectedPackageId' => $selectedPackageId, 'users' => User::whereIn('role', ['admin', 'manager', 'agent', 'staff'])->orderBy('name')->get(), 'stats' => $this->overviewStats()]);
    }

    public function storeAssignment(Request $request)
    {
        $data = $request->validate(['tour_schedule_id' => 'required|exists:tour_schedules,id', 'user_id' => 'required|exists:users,id', 'assignment_role' => 'required|string|max:100', 'status' => 'required|in:assigned,confirmed,completed', 'notes' => 'nullable|string']);
        StaffAssignment::create($data);
        return back()->with('success', 'Staff assignment added.');
    }

    public function calendar()
    {
        $schedules = TourSchedule::with(['tourPackage', 'assignments.user', 'allocations'])->where('schedule_date', '>=', now()->startOfMonth())->orderBy('schedule_date')->paginate(15);
        return view('tour-planning.index', ['module' => 'calendar', 'schedules' => $schedules, 'stats' => $this->overviewStats()]);
    }
}
