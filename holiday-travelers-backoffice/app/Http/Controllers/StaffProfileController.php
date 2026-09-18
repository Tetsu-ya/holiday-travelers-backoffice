<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffProfileController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $staff = User::query()
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $staffStats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'departments' => User::whereNotNull('department')->distinct('department')->count('department'),
        ];

        return view('staff-profiles.index', compact('staff', 'staffStats', 'search'));
    }

    public function create()
    {
        return view('staff-profiles.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);

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

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

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
            'role' => ['required', Rule::in(['admin', 'manager', 'agent', 'staff'])],
            'department' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}