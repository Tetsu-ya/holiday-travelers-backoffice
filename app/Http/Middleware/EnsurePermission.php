<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user && ($user->status ?? 'active') === 'active', 403, 'Your account is inactive.');

        $permission = $this->permissionFor($request->route()?->getName(), (string) $request->route('module'));
        abort_unless(!$permission || $user->hasPermission($permission), 403, 'You do not have permission to access this page.');

        return $next($request);
    }

    private function permissionFor(?string $route, string $module = ''): ?string
    {
        if (!$route || $route === 'dashboard') return 'view_dashboard';
        if ($route === 'module.placeholder') {
            foreach (['marketing' => 'manage_marketing', 'promotions' => 'manage_marketing', 'discount-codes' => 'manage_marketing', 'revenue' => 'view_reports', 'expenses' => 'view_reports', 'commissions' => 'view_reports', 'analytics-dashboard' => 'view_reports', 'customer-documents' => 'manage_documents', 'visa-applications' => 'manage_documents', 'visa-requirements' => 'manage_documents', 'document-checklist' => 'manage_documents', 'document-expiration' => 'manage_documents', 'visa-status' => 'manage_documents'] as $prefix => $permission) {
                if ($module === $prefix) return $permission;
            }
            return 'manage_tours';
        }
        foreach ([
            'staff-profiles.' => 'manage_staff', 'agent-profiles.' => 'manage_staff',
            'roles-permissions.' => 'manage_staff', 'tasks.' => 'manage_staff',
            'scheduling.' => 'manage_staff', 'performance.' => 'manage_staff',
            'packages.' => 'manage_tours', 'tour-schedule.' => 'manage_tours',
            'tour-availability.' => 'manage_tours', 'resource-allocation.' => 'manage_tours',
            'staff-assignment.' => 'manage_tours', 'resource-calendar.' => 'manage_tours',
            'ai-planning.' => 'manage_tours', 'bookings.' => 'manage_bookings',
            'payment-methods.' => 'manage_bookings', 'payments.' => 'manage_bookings',
            'partners.' => 'manage_partners', 'suppliers.' => 'manage_partners',
            'supplier-contracts.' => 'manage_partners', 'supplier-rates.' => 'manage_partners',
            'supplier-availability.' => 'manage_partners', 'supplier-performance.' => 'manage_partners',
            'campaigns.' => 'manage_marketing', 'leads.' => 'manage_marketing',
            'promotions.' => 'manage_marketing', 'discount-codes.' => 'manage_marketing',
            'marketing-calendar.' => 'manage_marketing', 'campaign-analytics.' => 'manage_marketing',
            'revenue.' => 'view_reports', 'expenses.' => 'view_reports',
            'commissions.' => 'view_reports', 'analytics-dashboard.' => 'view_reports',
            'reports.' => 'view_reports', 'customer-documents.' => 'manage_documents',
            'visa-applications.' => 'manage_documents', 'visa-requirements.' => 'manage_documents',
            'document-checklist.' => 'manage_documents', 'document-expiration.' => 'manage_documents',
            'visa-status.' => 'manage_documents',
        ] as $prefix => $permission) {
            if (str_starts_with($route, $prefix)) return $permission;
        }
        return null;
    }
}
