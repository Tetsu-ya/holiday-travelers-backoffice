<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\MarketingCampaign;
use App\Models\Promotion;
use Illuminate\Http\Request;

class MarketingOperationsController extends Controller
{
    public function promotions()
    {
        return view('marketing-operations.index', ['module' => 'promotions', 'records' => Promotion::latest()->paginate(15)]);
    }

    public function storePromotion(Request $request)
    {
        Promotion::create($request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string', 'discount_type' => 'required|in:percentage,fixed', 'discount_value' => 'required|numeric|min:0', 'starts_on' => 'required|date', 'ends_on' => 'required|date|after_or_equal:starts_on', 'status' => 'required|in:draft,active,expired']));
        return back()->with('success', 'Promotion added.');
    }

    public function discountCodes()
    {
        return view('marketing-operations.index', ['module' => 'discount-codes', 'records' => DiscountCode::latest()->paginate(15)]);
    }

    public function storeDiscountCode(Request $request)
    {
        DiscountCode::create($request->validate(['code' => 'required|string|max:50|unique:discount_codes,code', 'description' => 'nullable|string', 'discount_type' => 'required|in:percentage,fixed', 'discount_value' => 'required|numeric|min:0', 'usage_limit' => 'nullable|integer|min:1', 'starts_on' => 'required|date', 'ends_on' => 'required|date|after_or_equal:starts_on', 'status' => 'required|in:active,inactive,expired']));
        return back()->with('success', 'Discount code added.');
    }

    public function calendar()
    {
        $campaigns = MarketingCampaign::orderBy('start_date')->paginate(15);
        return view('marketing-operations.index', ['module' => 'calendar', 'records' => $campaigns]);
    }

    public function analytics()
    {
        $campaigns = MarketingCampaign::latest()->paginate(15);
        $analytics = [
            'spend' => MarketingCampaign::sum('actual_spend'),
            'budget' => MarketingCampaign::sum('budget'),
            'leads' => MarketingCampaign::sum('leads_generated'),
            'conversions' => MarketingCampaign::sum('conversions'),
            'byChannel' => MarketingCampaign::selectRaw('channel, SUM(actual_spend) as spend, SUM(leads_generated) as leads, SUM(conversions) as conversions')->groupBy('channel')->orderByDesc('conversions')->get(),
        ];
        return view('marketing-operations.index', compact('campaigns', 'analytics') + ['module' => 'analytics', 'records' => $campaigns]);
    }
}
