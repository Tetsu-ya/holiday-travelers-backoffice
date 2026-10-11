<?php

namespace App\Http\Controllers;

use App\Models\MarketingBrochure;
use App\Models\MarketingCampaign;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class MarketingCampaignController extends Controller
{
    public function index()
    {
        $campaigns = MarketingCampaign::latest()->paginate(15);
        $totalLeads = (int) MarketingCampaign::sum('leads_generated');
        $convertedLeads = (int) MarketingCampaign::sum('conversions');
        $campaignStats = [
            'active' => MarketingCampaign::where('status', 'active')->count(),
            'budget_used' => MarketingCampaign::sum('actual_spend'),
            'leads' => $totalLeads,
            'conversion_rate' => $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0,
        ];

        return view('marketing.campaigns.index', compact('campaigns', 'campaignStats'));
    }

    public function create()
    {
        $brochures = MarketingBrochure::orderBy('title')->get();
        $packages = TourPackage::orderBy('name')->get();

        return view('marketing.campaigns.create', compact('brochures', 'packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:facebook,instagram,tiktok,social_media',
            'tour_package_id' => 'nullable|exists:tour_packages,id',
            'marketing_brochure_id' => 'nullable|exists:marketing_brochures,id',
            'budget' => 'nullable|numeric|min:0',
            'actual_spend' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,paused,completed',
            'leads_generated' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
        ]);

        MarketingCampaign::create($data);

        return redirect()->route('campaign-analytics.index', ['tab' => 'social-ads'])
            ->with('success', 'Social media campaign created.');
    }

    public function show(MarketingCampaign $campaign)
    {
        return view('marketing.campaigns.show', compact('campaign'));
    }

    public function edit(MarketingCampaign $campaign)
    {
        $brochures = MarketingBrochure::orderBy('title')->get();

        return view('marketing.campaigns.edit', compact('campaign', 'brochures'));
    }

    public function update(Request $request, MarketingCampaign $campaign)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:email,social_media,facebook,instagram,tiktok,referral,ads,events',
            'tour_package_id' => 'nullable|exists:tour_packages,id',
            'marketing_brochure_id' => 'nullable|exists:marketing_brochures,id',
            'budget' => 'nullable|numeric|min:0',
            'actual_spend' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,active,paused,completed',
            'leads_generated' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
        ]);

        $campaign->update($data);
        return redirect()->route('campaigns.index')->with('success', 'Campaign updated.');
    }

    public function destroy(MarketingCampaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }
}
