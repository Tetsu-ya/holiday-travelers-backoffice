<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\Expense;
use App\Models\MarketingCampaign;
use App\Models\PartnerCommission;
use App\Models\Payment;
use Illuminate\Http\Request;

class FinancialOperationsController extends Controller
{
    public function revenue()
    {
        $revenue = Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount');
        $payments = Payment::with('booking')->whereIn('status', ['paid', 'partial'])->latest('paid_at')->paginate(15);
        return view('financial-operations.index', compact('payments', 'revenue') + ['module' => 'revenue', 'refunds' => Payment::where('status', 'refunded')->sum('amount')]);
    }

    public function expenses()
    {
        return view('financial-operations.index', ['module' => 'expenses', 'records' => Expense::latest('expense_date')->paginate(15), 'totalExpenses' => Expense::sum('amount')]);
    }

    public function storeExpense(Request $request)
    {
        Expense::create($request->validate(['description' => 'required|string|max:255', 'category' => 'required|string|max:100', 'amount' => 'required|numeric|min:0.01', 'expense_date' => 'required|date', 'status' => 'required|in:recorded,approved,paid', 'notes' => 'nullable|string']));
        return back()->with('success', 'Expense recorded.');
    }

    public function commissions()
    {
        $commissions = PartnerCommission::with(['partner', 'booking'])->latest()->paginate(15);
        $totals = ['pending' => PartnerCommission::where('status', 'pending')->sum('commission_amount'), 'approved' => PartnerCommission::where('status', 'approved')->sum('commission_amount'), 'paid' => PartnerCommission::where('status', 'paid')->sum('commission_amount')];
        return view('financial-operations.index', compact('commissions', 'totals') + ['module' => 'commissions']);
    }

    public function analytics()
    {
        $revenue = Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount');
        $expenses = Expense::sum('amount');
        $commissions = PartnerCommission::whereIn('status', ['approved', 'paid'])->sum('commission_amount');
        $campaignSpend = MarketingCampaign::sum('actual_spend');
        $monthlyRevenue = collect(range(1, 12))->map(fn ($month) => Booking::whereYear('travel_date', now()->year)->whereMonth('travel_date', $month)->whereIn('status', ['confirmed', 'completed'])->sum('total_amount'));
        $analytics = compact('revenue', 'expenses', 'commissions', 'campaignSpend', 'monthlyRevenue');
        return view('financial-operations.index', compact('analytics') + ['module' => 'analytics']);
    }
}
