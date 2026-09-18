<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolePermissionController extends Controller
{
    private array $permissions = [
        'view_dashboard' => 'View dashboard', 'manage_staff' => 'Manage staff profiles',
        'manage_tours' => 'Manage tour packages', 'manage_bookings' => 'Manage bookings',
        'manage_partners' => 'Manage suppliers and partners', 'manage_marketing' => 'Manage marketing',
        'view_reports' => 'View financial reports',
    ];

    public function index(Request $request)
    {
        $roles = ['admin', 'manager', 'agent', 'staff'];
        $rolePermissions = collect($roles)->mapWithKeys(fn ($role) => [$role => $this->permissionsFor($role)]);
        $selectedRole = in_array($request->query('role'), $roles, true) ? $request->query('role') : 'staff';
        return view('roles-permissions.index', compact('roles', 'rolePermissions', 'selectedRole') + ['permissions' => $this->permissions]);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'manager', 'agent', 'staff'])], 'permissions' => ['array']]);
        $permissions = array_keys(array_filter($data['permissions'] ?? []));
        $stored = json_decode((string) $request->session()->get('role_permissions'), true) ?: [];
        $stored[$data['role']] = $permissions;
        $request->session()->put('role_permissions', json_encode($stored));
        return redirect()->route('roles-permissions.index', ['role' => $data['role']])->with('success', 'Role permissions updated for ' . ucfirst($data['role']) . '.');
    }

    private function permissionsFor(string $role): array
    {
        $stored = json_decode((string) request()->session()->get('role_permissions'), true) ?: [];
        if (isset($stored[$role])) return $stored[$role];
        return match ($role) {
            'admin' => array_keys($this->permissions),
            'manager' => ['view_dashboard', 'manage_staff', 'manage_tours', 'manage_bookings', 'manage_partners', 'manage_marketing', 'view_reports'],
            'agent' => ['view_dashboard', 'manage_bookings', 'view_reports'],
            default => ['view_dashboard', 'manage_bookings'],
        };
    }
}
