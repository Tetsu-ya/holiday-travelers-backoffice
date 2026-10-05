<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStaffAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class BookingStaffAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['tourPackage', 'staffAssignments.user'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('travel_date')
            ->paginate(15);

        return view('booking-staff-assignments.index', [
            'bookings' => $bookings,
            'bookingOptions' => Booking::with('tourPackage')
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->orderBy('travel_date')
                ->get(),
            'users' => $this->staffUsers(),
            'selectedBooking' => $request->integer('booking_id') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'user_id' => 'required|exists:users,id',
            'assignment_role' => 'required|string|max:100',
            'status' => 'required|in:assigned,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        BookingStaffAssignment::updateOrCreate(
            ['booking_id' => $data['booking_id'], 'user_id' => $data['user_id'], 'assignment_role' => $data['assignment_role']],
            ['status' => $data['status'], 'notes' => $data['notes'] ?? null]
        );

        return back()->with('success', 'Staff member assigned to the booking.');
    }

    public function update(Request $request, BookingStaffAssignment $assignment)
    {
        $data = $request->validate([
            'status' => 'required|in:assigned,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        $assignment->update($data);

        return back()->with('success', 'Booking assignment updated.');
    }

    public function destroy(BookingStaffAssignment $assignment)
    {
        $assignment->delete();

        return back()->with('success', 'Staff member removed from the booking.');
    }

    private function staffUsers()
    {
        return User::whereIn('role', ['admin', 'manager', 'agent', 'tour_guide', 'driver', 'staff'])
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'active');
            })
            ->orderBy('name')
            ->get();
    }
}
