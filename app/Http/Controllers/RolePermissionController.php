<?php

namespace App\Http\Controllers;

use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolePermissionController extends Controller
{
    private array $permissions = [
        'view_dashboard' => 'View dashboard', 'manage_staff' => 'Manage staff profiles',
        'manage_tours' => 'Manage tour packages', 'manage_bookings' => 'Manage bookings',
        'manage_partners' => 'Manage suppliers and partners', 'manage_marketing' => 'Manage marketing',
        'manage_documents' => 'Manage documents and visa assistance', 'view_reports' => 'View financial reports',
    ];

    public function index(Request $request)
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Only administrators can view permissions.');
        $roles = ['admin', 'manager', 'agent', 'staff'];
        $rolePermissions = collect($roles)->mapWithKeys(fn ($role) => [$role => $this->permissionsFor($role)]);
        $selectedRole = in_array($request->query('role'), $roles, true) ? $request->query('role') : 'staff';
        return view('roles-permissions.index', compact('roles', 'rolePermissions', 'selectedRole') + ['permissions' => $this->permissions]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Only administrators can change permissions.');
        $data = $request->validate(['role' => ['required', Rule::in(['admin', 'manager', 'agent', 'staff'])], 'permissions' => ['array']]);
        $permissions = array_keys(array_filter($data['permissions'] ?? []));
        if ($data['role'] === 'admin') $permissions = array_keys($this->permissions);
        RolePermission::updateOrCreate(['role' => $data['role']], ['permissions' => $permissions]);
        return redirect()->route('roles-permissions.index', ['role' => $data['role']])->with('success', 'Role permissions updated for ' . ucfirst($data['role']) . '.');
    }

    private function permissionsFor(string $role): array
    {
        $stored = RolePermission::where('role', $role)->value('permissions');
        if ($stored !== null) return (array) $stored;
        return match ($role) {
            'admin' => array_keys($this->permissions),
            'manager' => ['view_dashboard', 'manage_staff', 'manage_tours', 'manage_bookings', 'manage_partners', 'manage_marketing', 'manage_documents', 'view_reports'],
            'agent' => ['view_dashboard', 'manage_bookings', 'manage_documents'],
            default => ['view_dashboard', 'manage_bookings'],
        };
    }
}
