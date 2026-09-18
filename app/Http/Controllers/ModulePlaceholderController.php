<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModulePlaceholderController extends Controller
{
    public function __invoke(Request $request, string $module)
    {
        $moduleLabels = [
            'staff-profiles' => 'Staff Profiles',
            'agent-profiles' => 'Agent Profiles',
            'roles-permissions' => 'Roles & Permissions',
            'tasks' => 'Tasks',
            'scheduling' => 'Scheduling',
            'staff-performance' => 'Staff Performance',
            'staff-commissions' => 'Staff Commissions',
            'contracts' => 'Contracts',
            'supplier-rates' => 'Supplier Rates',
            'supplier-availability' => 'Supplier Availability',
            'supplier-performance' => 'Supplier Performance',
            'tour-schedule' => 'Tour Schedule',
            'tour-availability' => 'Tour Availability',
            'resource-allocation' => 'Resource Allocation',
            'staff-assignment' => 'Staff Assignment',
            'resource-calendar' => 'Resource Calendar',
            'promotions' => 'Promotions',
            'discount-codes' => 'Discount Codes',
            'marketing-calendar' => 'Marketing Calendar',
            'campaign-analytics' => 'Campaign Analytics',
            'revenue' => 'Revenue',
            'expenses' => 'Expenses',
            'commissions' => 'Commissions',
            'analytics-dashboard' => 'Analytics Dashboard',
            'customer-documents' => 'Customer Documents',
            'visa-applications' => 'Visa Applications',
            'visa-requirements' => 'Visa Requirements',
            'document-checklist' => 'Document Checklist',
            'document-expiration' => 'Document Expiration Tracking',
            'visa-status' => 'Visa Application Status',
        ];

        abort_unless(isset($moduleLabels[$module]), 404);

        return view('modules.placeholder', [
            'module' => $moduleLabels[$module],
        ]);
    }
}