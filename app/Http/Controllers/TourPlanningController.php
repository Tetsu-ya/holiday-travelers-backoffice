<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ResourceAllocation;
use App\Models\StaffAssignment;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class TourPlanningController extends Controller
{
    public function schedule()
    {
        $schedules = TourSchedule::with('tourPackage')->orderBy('schedule_date')->paginate(15);
        return view('tour-planning.index', ['module' => 'schedule', 'schedules' => $schedules, 'packages' => TourPackage::orderBy('name')->get()]);
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id', 'schedule_date' => 'required|date',
            'starts_at' => 'nullable|date_format:H:i', 'ends_at' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255', 'capacity' => 'required|integer|min:1',
            'status' => 'required|in:planned,confirmed,cancelled,completed', 'notes' => 'nullable|string',
        ]);
        TourSchedule::create($data);
        return back()->with('success', 'Tour schedule added.');
    }

    public function availability()
    {
        $packages = TourPackage::withSum(['bookings as booked_pax' => fn ($query) => $query->whereIn('status', ['pending', 'confirmed'])], 'pax')->latest()->paginate(15);
        return view('tour-planning.index', ['module' => 'availability', 'packages' => $packages]);
    }

    public function allocation()
    {
        return view('tour-planning.index', ['module' => 'allocation', 'allocations' => ResourceAllocation::with('tourSchedule.tourPackage')->latest()->paginate(15), 'schedules' => TourSchedule::with('tourPackage')->orderBy('schedule_date')->get()]);
    }

    public function storeAllocation(Request $request)
    {
        ResourceAllocation::create($request->validate(['tour_schedule_id' => 'required|exists:tour_schedules,id', 'resource_type' => 'required|string|max:100', 'resource_name' => 'required|string|max:255', 'quantity' => 'required|integer|min:1', 'status' => 'required|in:reserved,requested,confirmed', 'notes' => 'nullable|string']));
        return back()->with('success', 'Resource allocation added.');
    }

    public function assignment()
    {
        return view('tour-planning.index', ['module' => 'assignment', 'assignments' => StaffAssignment::with(['tourSchedule.tourPackage', 'user'])->latest()->paginate(15), 'schedules' => TourSchedule::with('tourPackage')->orderBy('schedule_date')->get(), 'users' => User::whereIn('role', ['admin', 'manager', 'agent', 'staff'])->orderBy('name')->get()]);
    }

    public function storeAssignment(Request $request)
    {
        StaffAssignment::create($request->validate(['tour_schedule_id' => 'required|exists:tour_schedules,id', 'user_id' => 'required|exists:users,id', 'assignment_role' => 'required|string|max:100', 'status' => 'required|in:assigned,confirmed,completed', 'notes' => 'nullable|string']));
        return back()->with('success', 'Staff assignment added.');
    }

    public function calendar()
    {
        $schedules = TourSchedule::with(['tourPackage', 'assignments.user', 'allocations'])->where('schedule_date', '>=', now()->startOfMonth())->orderBy('schedule_date')->paginate(15);
        return view('tour-planning.index', ['module' => 'calendar', 'schedules' => $schedules]);
    }
}
