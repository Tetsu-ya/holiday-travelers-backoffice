<?php

namespace App\Http\Controllers;

use App\Models\MarketingCampaign;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:email,social_media,referral,ads,events',
            'tour_package_id' => 'nullable|exists:tour_packages,id',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        MarketingCampaign::create($data);
        return redirect()->route('campaigns.index')->with('success', 'Campaign created.');
    }

    public function show(MarketingCampaign $campaign)
    {
        return view('marketing.campaigns.show', compact('campaign'));
    }

    public function edit(MarketingCampaign $campaign)
    {
        return view('marketing.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, MarketingCampaign $campaign)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:email,social_media,referral,ads,events',
            'tour_package_id' => 'nullable|exists:tour_packages,id',
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

    public function create()
    {
        return view('marketing.campaigns.create');
    }
}
