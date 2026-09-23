<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\Expense;
use App\Models\MarketingCampaign;
use App\Models\PartnerCommission;
use App\Models\Payment;
use App\Support\Revenue;
use Illuminate\Http\Request;

class FinancialOperationsController extends Controller
{
    public function revenue()
    {
        $revenue = Revenue::collected();
        $payments = Payment::with('booking')->whereIn('status', Revenue::RECEIVED)->latest('paid_at')->paginate(15);

        return view('financial-operations.index', compact('payments', 'revenue') + [
            'module' => 'revenue',
            'refunds' => Revenue::refunded(),
            'outstanding' => Revenue::outstanding(),
            'bookedValue' => Revenue::bookedValue(),
        ]);
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
        Booking::with('businessPartner')
            ->whereNotNull('business_partner_id')
            ->get()
            ->each(function (Booking $booking) {
                $rate = (float) ($booking->businessPartner?->commission_rate ?? 0);

                if ($rate <= 0) {
                    PartnerCommission::where('booking_id', $booking->id)
                        ->whereIn('status', ['pending', 'approved'])
                        ->delete();
                    return;
                }

                $commission = PartnerCommission::where('booking_id', $booking->id)->first()
                    ?? new PartnerCommission(['status' => 'pending']);
                $commission->booking_id = $booking->id;
                $commission->partner_id = $booking->business_partner_id;
                $commission->commission_rate = $rate;
                $commission->commission_amount = round((float) $booking->total_amount * ($rate / 100), 2);
                $commission->save();
            });

        $commissions = PartnerCommission::with(['partner', 'booking'])->latest()->paginate(15);
        $totals = ['pending' => PartnerCommission::where('status', 'pending')->sum('commission_amount'), 'approved' => PartnerCommission::where('status', 'approved')->sum('commission_amount'), 'paid' => PartnerCommission::where('status', 'paid')->sum('commission_amount')];
        return view('financial-operations.index', compact('commissions', 'totals') + ['module' => 'commissions']);
    }

    public function approveCommission(PartnerCommission $commission)
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Only pending commissions can be approved.');
        }

        $commission->update(['status' => 'approved']);

        return back()->with('success', 'Commission approved.');
    }

    public function markCommissionPaid(PartnerCommission $commission)
    {
        if ($commission->status !== 'approved') {
            return back()->with('error', 'Only approved commissions can be marked as paid.');
        }

        $commission->update(['status' => 'paid']);

        return back()->with('success', 'Commission marked as paid.');
    }

    public function analytics()
    {
        $revenue = Revenue::collected();
        $expenses = Expense::sum('amount');
        $commissions = PartnerCommission::whereIn('status', ['approved', 'paid'])->sum('commission_amount');
        $campaignSpend = MarketingCampaign::sum('actual_spend');

        $analytics = [
            'revenue' => $revenue,
            'grossCollected' => Revenue::grossCollected(),
            'refunds' => Revenue::refunded(),
            'bookedValue' => Revenue::bookedValue(),
            'outstanding' => Revenue::outstanding(),
            'expenses' => $expenses,
            'commissions' => $commissions,
            'campaignSpend' => $campaignSpend,
            'monthlyRevenue' => Revenue::monthlyCollected(),
            'monthlyByTravelMonth' => Revenue::monthlyCollectedByTravelMonth(),
            'revenueBasis' => 'cash',
        ];

        return view('financial-operations.index', compact('analytics') + ['module' => 'analytics']);
    }
}
