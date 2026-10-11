<?php

namespace App\Http\Controllers;

use App\Models\StaffAssignment;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class BookingStaffAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $selectedPackage = $request->integer('package_id') ?: null;
        $schedules = TourSchedule::with(['tourPackage', 'assignments.user'])
            ->whereDate('schedule_date', '>=', today())
            ->when($selectedPackage, fn ($query) => $query->where('tour_package_id', $selectedPackage))
            ->orderBy('schedule_date')->paginate(15)->withQueryString();

        return view('booking-staff-assignments.index', [
            'schedules' => $schedules,
            'packages' => TourPackage::where('status', 'published')->orderBy('name')->get(),
            'users' => $this->staffUsers(),
            'selectedPackage' => $selectedPackage,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tour_schedule_id' => 'required|exists:tour_schedules,id',
            'user_id' => 'required|exists:users,id',
            'assignment_role' => 'required|string|max:100',
            'status' => 'required|in:assigned,confirmed,completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        StaffAssignment::updateOrCreate(
            ['tour_schedule_id' => $data['tour_schedule_id'], 'user_id' => $data['user_id']],
            ['assignment_role' => $data['assignment_role'], 'status' => $data['status'], 'notes' => $data['notes'] ?? null]
        );

        return back()->with('success', 'Staff member assigned to the tour departure.');
    }

    public function update(Request $request, StaffAssignment $assignment)
    {
        $assignment->update($request->validate([
            'assignment_role' => 'required|string|max:100',
            'status' => 'required|in:assigned,confirmed,completed',
            'notes' => 'nullable|string|max:2000',
        ]));

        return back()->with('success', 'Tour staff assignment updated.');
    }

    public function destroy(StaffAssignment $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Staff member removed from the tour.');
    }

    private function staffUsers()
    {
        return User::whereIn('role', ['admin', 'manager', 'agent', 'tour_guide', 'driver', 'staff'])
            ->where(fn ($query) => $query->whereNull('status')->orWhere('status', 'active'))
            ->orderBy('name')->get();
    }
}
