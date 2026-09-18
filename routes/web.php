<?php

use App\Http\Controllers\AIResourcePlanningController;
use App\Http\Controllers\BusinessPartnerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModulePlaceholderController;
use App\Http\Controllers\AgentProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StaffOperationController;
use App\Http\Controllers\TourPlanningController;
use App\Http\Controllers\SupplierOperationsController;
use App\Http\Controllers\MarketingOperationsController;
use App\Http\Controllers\FinancialOperationsController;
use App\Http\Controllers\DocumentOperationsController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'store']);
    Route::get('login/verify', [\App\Http\Controllers\Auth\LoginController::class, 'showVerificationForm'])->name('login.verify');
    Route::post('login/verify', [\App\Http\Controllers\Auth\LoginController::class, 'verifyLogin']);
    Route::get('auth/google/redirect', [\App\Http\Controllers\Auth\LoginController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('auth/google/callback', [\App\Http\Controllers\Auth\LoginController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Staff Profiles
    Route::resource('staff-profiles', \App\Http\Controllers\StaffProfileController::class)
        ->parameters(['staff-profiles' => 'user']);
    Route::get('agent-profiles', [AgentProfileController::class, 'index'])->name('agent-profiles.index');
    Route::get('roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('roles-permissions', [RolePermissionController::class, 'update'])->name('roles-permissions.update');
    Route::resource('tasks', StaffOperationController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['tasks' => 'record']);
    Route::resource('scheduling', StaffOperationController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['scheduling' => 'record']);
    Route::resource('performance', StaffOperationController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['performance' => 'record']);

    // Tour availability and resource planning
    Route::get('tour-schedule', [TourPlanningController::class, 'schedule'])->name('tour-schedule.index');
    Route::post('tour-schedule', [TourPlanningController::class, 'storeSchedule'])->name('tour-schedule.store');
    Route::get('tour-availability', [TourPlanningController::class, 'availability'])->name('tour-availability.index');
    Route::get('resource-allocation', [TourPlanningController::class, 'allocation'])->name('resource-allocation.index');
    Route::post('resource-allocation', [TourPlanningController::class, 'storeAllocation'])->name('resource-allocation.store');
    Route::get('staff-assignment', [TourPlanningController::class, 'assignment'])->name('staff-assignment.index');
    Route::post('staff-assignment', [TourPlanningController::class, 'storeAssignment'])->name('staff-assignment.store');
    Route::get('resource-calendar', [TourPlanningController::class, 'calendar'])->name('resource-calendar.index');

    // Supplier operations
    Route::get('supplier-contracts', [SupplierOperationsController::class, 'contracts'])->name('supplier-contracts.index');
    Route::post('supplier-contracts', [SupplierOperationsController::class, 'storeContract'])->name('supplier-contracts.store');
    Route::get('supplier-rates', [SupplierOperationsController::class, 'rates'])->name('supplier-rates.index');
    Route::post('supplier-rates', [SupplierOperationsController::class, 'storeRate'])->name('supplier-rates.store');
    Route::get('supplier-availability', [SupplierOperationsController::class, 'availability'])->name('supplier-availability.index');
    Route::post('supplier-availability', [SupplierOperationsController::class, 'storeAvailability'])->name('supplier-availability.store');
    Route::get('supplier-performance', [SupplierOperationsController::class, 'performance'])->name('supplier-performance.index');
    Route::post('supplier-performance', [SupplierOperationsController::class, 'storePerformance'])->name('supplier-performance.store');

    // Marketing operations
    Route::get('promotions', [MarketingOperationsController::class, 'promotions'])->name('promotions.index');
    Route::post('promotions', [MarketingOperationsController::class, 'storePromotion'])->name('promotions.store');
    Route::get('discount-codes', [MarketingOperationsController::class, 'discountCodes'])->name('discount-codes.index');
    Route::post('discount-codes', [MarketingOperationsController::class, 'storeDiscountCode'])->name('discount-codes.store');
    Route::get('marketing-calendar', [MarketingOperationsController::class, 'calendar'])->name('marketing-calendar.index');
    Route::get('campaign-analytics', [MarketingOperationsController::class, 'analytics'])->name('campaign-analytics.index');

    // Financial operations
    Route::get('revenue', [FinancialOperationsController::class, 'revenue'])->name('revenue.index');
    Route::get('expenses', [FinancialOperationsController::class, 'expenses'])->name('expenses.index');
    Route::post('expenses', [FinancialOperationsController::class, 'storeExpense'])->name('expenses.store');
    Route::get('commissions', [FinancialOperationsController::class, 'commissions'])->name('commissions.index');
    Route::get('analytics-dashboard', [FinancialOperationsController::class, 'analytics'])->name('analytics-dashboard.index');

    // Document and visa assistance
    Route::get('customer-documents', [DocumentOperationsController::class, 'documents'])->name('customer-documents.index');
    Route::post('customer-documents', [DocumentOperationsController::class, 'storeDocument'])->name('customer-documents.store');
    Route::get('visa-applications', [DocumentOperationsController::class, 'applications'])->name('visa-applications.index');
    Route::post('visa-applications', [DocumentOperationsController::class, 'storeApplication'])->name('visa-applications.store');
    Route::get('visa-requirements', [DocumentOperationsController::class, 'requirements'])->name('visa-requirements.index');
    Route::post('visa-requirements', [DocumentOperationsController::class, 'storeRequirement'])->name('visa-requirements.store');
    Route::get('document-checklist', [DocumentOperationsController::class, 'checklist'])->name('document-checklist.index');
    Route::get('document-expiration', [DocumentOperationsController::class, 'expiration'])->name('document-expiration.index');
    Route::get('visa-status', [DocumentOperationsController::class, 'status'])->name('visa-status.index');

    // Tour Packages & Bookings
    Route::resource('packages', \App\Http\Controllers\TourPackageController::class);
    Route::get('bookings/{booking}/finance', [\App\Http\Controllers\BookingController::class, 'finance'])->name('bookings.finance');
    Route::post('bookings/{booking}/finance/payment', [\App\Http\Controllers\BookingController::class, 'recordPayment'])->name('bookings.finance.payment');
    Route::post('bookings/{booking}/finance/invoice', [\App\Http\Controllers\BookingController::class, 'generateInvoice'])->name('bookings.finance.invoice');
    Route::resource('bookings', \App\Http\Controllers\BookingController::class);

    // Business Partners
    Route::resource('partners', BusinessPartnerController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Marketing
    Route::resource('marketing/campaigns', \App\Http\Controllers\MarketingCampaignController::class)
        ->parameters(['campaigns' => 'campaign'])->names('campaigns');
    Route::resource('marketing/leads', \App\Http\Controllers\LeadController::class)
        ->parameters(['leads' => 'lead'])->names('leads');

    // AI-Assisted Resource Planning
    Route::get('ai-planning', [AIResourcePlanningController::class, 'index'])->name('ai-planning.index');
    Route::post('ai-planning/forecast-demand', [AIResourcePlanningController::class, 'forecastDemand'])->name('ai-planning.forecast-demand');
    Route::post('ai-planning/recommend-supplier', [AIResourcePlanningController::class, 'recommendSupplier'])->name('ai-planning.recommend-supplier');
    Route::post('ai-planning/recommend-marketing-budget', [AIResourcePlanningController::class, 'recommendMarketingBudget'])->name('ai-planning.recommend-budget');

    // Reports
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/financial-export', [\App\Http\Controllers\ReportController::class, 'financialExport'])->name('reports.financial-export');

    // Navigation entries whose full workflows are scheduled for a later module release.
    Route::get('modules/{module}', ModulePlaceholderController::class)
        ->where('module', '[a-z0-9-]+')
        ->name('module.placeholder');
});
