<?php

namespace App\Http\Controllers;

use App\Models\AiResourcePlan;
use App\Models\TourPackage;
use App\Services\AIResourcePlanningService;
use Illuminate\Http\Request;

class AIResourcePlanningController extends Controller
{
    public function index()
    {
        $plans = AiResourcePlan::latest()->paginate(10);
        $packages = TourPackage::published()->get() ?? TourPackage::all();
        return view('ai-planning.index', compact('plans', 'packages'));
    }

    public function forecastDemand(Request $request, AIResourcePlanningService $ai)
    {
        $package = TourPackage::findOrFail($request->input('package_id'));
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
