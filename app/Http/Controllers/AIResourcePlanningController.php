<?php

namespace App\Http\Controllers;

use App\Models\AiResourcePlan;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Services\AIResourcePlanningService;
use App\Services\TravelReadinessService;
use Illuminate\Http\Request;

class AIResourcePlanningController extends Controller
{
    public function index()
    {
        $plans = AiResourcePlan::latest()->paginate(10);
        $packages = TourPackage::published()->orderBy('name')->get();

        // Keep the forecast available while packages are still in draft during setup.
        if ($packages->isEmpty()) {
            $packages = TourPackage::whereIn('status', ['draft', 'published'])
                ->orderBy('name')
                ->get();
        }

        $schedules = TourSchedule::with('tourPackage')
            ->whereDate('schedule_date', '>=', today())
            ->whereIn('status', ['planned', 'confirmed'])
            ->orderBy('schedule_date')->get();

        return view('ai-planning.index', compact('plans', 'packages', 'schedules'));
    }

    public function assessReadiness(Request $request, TravelReadinessService $readiness)
    {
        $schedule = TourSchedule::with('tourPackage')->findOrFail($request->validate([
            'schedule_id' => ['required', 'integer', 'exists:tour_schedules,id'],
        ])['schedule_id']);

        return back()->with('travel_readiness', $readiness->assess($schedule));
    }

    public function forecastDemand(Request $request, AIResourcePlanningService $ai)
    {
        $package = TourPackage::whereIn('status', ['draft', 'published'])
            ->findOrFail($request->validate([
                'package_id' => ['required', 'integer', 'exists:tour_packages,id'],
            ])['package_id']);

        $ai->forecastDemand($package);
        return back()->with('success', 'Demand forecast generated.');
    }

    public function recommendSupplier(Request $request, AIResourcePlanningService $ai)
    {
        $ai->recommendSupplier($request->input('category'), $request->input('location'));
        return back()->with('success', 'Supplier recommendation generated.');
    }

    public function recommendMarketingBudget(AIResourcePlanningService $ai)
    {
        $ai->recommendMarketingBudget();
        return back()->with('success', 'Marketing budget recommendation generated.');
    }
}
