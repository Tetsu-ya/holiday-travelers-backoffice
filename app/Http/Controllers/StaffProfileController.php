<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StaffProfileController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $role = $request->input('role');
        $status = $request->input('status');
        $staff = User::query()
            ->with('bookingStaffAssignments.booking.tourPackage')
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when(in_array($role, ['admin', 'manager', 'agent', 'tour_guide', 'driver', 'staff'], true), fn ($query) => $query->where('role', $role))
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $legacyRoleTitles = User::whereNotIn('role', ['agent', 'tour_guide', 'driver'])
            ->whereNotNull('job_title');
        $staffStats = [
            'total' => User::count(),
            'tour_guides' => User::where('role', 'tour_guide')->count() + (clone $legacyRoleTitles)->whereRaw('LOWER(job_title) LIKE ?', ['%tour guide%'])->count(),
            'drivers' => User::where('role', 'driver')->count() + (clone $legacyRoleTitles)->whereRaw('LOWER(job_title) LIKE ?', ['%driver%'])->count(),
            'travel_agents' => User::where('role', 'agent')->count() + (clone $legacyRoleTitles)->whereRaw('LOWER(job_title) LIKE ?', ['%travel agent%'])->count(),
            'others' => 0,
        ];
        $staffStats['others'] = max(0, $staffStats['total'] - $staffStats['tour_guides'] - $staffStats['drivers'] - $staffStats['travel_agents']);

        return view('staff-profiles.index', compact('staff', 'staffStats', 'search', 'role', 'status'));
    }

    public function create()
    {
        return view('staff-profiles.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make(Str::random(40));

        User::create($data);

        return redirect()->route('staff-profiles.index')->with('success', 'Staff profile added.');
    }

    public function show(User $user)
    {
        return view('staff-profiles.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('staff-profiles.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        unset($data['password']);

        $user->update($data);

        return redirect()->route('staff-profiles.show', $user)->with('success', 'Staff profile updated.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($request->user()->is($user), 403, 'You cannot delete your own staff profile.');

        $user->delete();

        return redirect()->route('staff-profiles.index')->with('success', 'Staff profile removed.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(['admin', 'manager', 'agent', 'tour_guide', 'driver', 'staff'])],
            'department' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
