<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::latest()->paginate(15);
        $leadStats = [
            'new' => Lead::where('status', 'new')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
            'conversion_rate' => Lead::count() > 0 ? (Lead::where('status', 'converted')->count() / Lead::count()) * 100 : 0,
            'contacted' => Lead::where('status', 'contacted')->count(),
            'lost' => Lead::where('status', 'lost')->count(),
        ];

        return view('marketing.leads.index', compact('leads', 'leadStats'));
    }

    public function create()
    {
        return view('marketing.leads.create', [
            'campaigns' => MarketingCampaign::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'marketing_campaign_id' => 'nullable|exists:marketing_campaigns,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'interested_package' => 'nullable|string|max:255',
            'status' => 'required|in:new,contacted,qualified,converted,lost',
        ]);

        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead added.');
    }

    public function show(Lead $lead)
    {
        return view('marketing.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        return view('marketing.leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($request->validate([
            'marketing_campaign_id' => 'nullable|exists:marketing_campaigns,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'interested_package' => 'nullable|string|max:255',
            'status' => 'required|in:new,contacted,qualified,converted,lost',
        ]));

        return redirect()->route('leads.index')->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }
}