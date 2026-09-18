<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\Expense;
use App\Models\MarketingCampaign;
use App\Models\PartnerCommission;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $revenue = Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount');
        $refunds = Payment::where('status', 'refunded')->sum('amount');
        $partnersPaid = BusinessPartner::query()
            ->join('partner_commissions', 'business_partners.id', '=', 'partner_commissions.partner_id')
            ->where('partner_commissions.status', 'paid')
            ->sum('partner_commissions.commission_amount');
        $campaignSpend = MarketingCampaign::sum('actual_spend');
        $netMargin = $revenue > 0
            ? (($revenue - $partnersPaid - $campaignSpend) / $revenue) * 100
            : 0;

        $monthlyRevenue = collect(range(1, 12))->mapWithKeys(function (int $month) {
            return [$month => Booking::whereYear('travel_date', now()->year)
                ->whereMonth('travel_date', $month)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_amount')];
        });

        $channelConversions = MarketingCampaign::select('channel')
            ->selectRaw('SUM(conversions) as conversions')
            ->groupBy('channel')
            ->orderByDesc('conversions')
            ->get();
        $totalConversions = $channelConversions->sum('conversions');

        $reportStats = compact('revenue', 'netMargin', 'refunds', 'partnersPaid', 'monthlyRevenue', 'channelConversions', 'totalConversions');

        return view('reports.index', compact('reportStats'));
    }

    public function export()
    {
        $bookings = Booking::with(['tourPackage', 'businessPartner'])
            ->latest('travel_date')
            ->get();

        return response()->streamDownload(function () use ($bookings) {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'Reference No',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Tour Package',
                'Destination',
                'Business Partner',
                'Passengers',
                'Travel Date',
                'Total Amount',
                'Payment Status',
                'Booking Status',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($output, [
                    $booking->reference_no,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone,
                    $booking->tourPackage?->name,
                    $booking->tourPackage?->destination,
                    $booking->businessPartner?->name,
                    $booking->pax,
                    $booking->travel_date?->format('Y-m-d'),
                    $booking->total_amount,
                    $booking->payment_status,
                    $booking->status,
                ]);
            }

            fclose($output);
        }, 'travel-performance-report-' . now()->format('Y-m-d_H-i-s') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function financialExport(Request $request)
    {
        $filters = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = $filters['from'];
        $to = $filters['to'];
        $bookingStatuses = ['confirmed', 'completed'];

        $revenue = Booking::whereBetween('travel_date', [$from, $to])
            ->whereIn('status', $bookingStatuses)
            ->sum('total_amount');
        $refunds = Payment::whereBetween('paid_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->where('status', 'refunded')
            ->sum('amount');
        $expenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $commissions = PartnerCommission::whereIn('status', ['approved', 'paid'])
            ->whereHas('booking', fn ($query) => $query->whereBetween('travel_date', [$from, $to]))
            ->sum('commission_amount');
        $campaignSpend = MarketingCampaign::where(function ($query) use ($from, $to) {
            $query->whereNull('start_date')->orWhere('start_date', '<=', $to);
        })->where(function ($query) use ($from) {
            $query->whereNull('end_date')->orWhere('end_date', '>=', $from);
        })->sum('actual_spend');
        $netResult = $revenue - $refunds - $expenses - $commissions - $campaignSpend;

        $expenseRows = Expense::whereBetween('expense_date', [$from, $to])
            ->orderBy('expense_date')
            ->get();

        return response()->streamDownload(function () use ($from, $to, $revenue, $refunds, $expenses, $commissions, $campaignSpend, $netResult, $expenseRows) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Financial Report']);
            fputcsv($output, ['Period', $from . ' to ' . $to]);
            fputcsv($output, []);
            fputcsv($output, ['Metric', 'Amount']);
            fputcsv($output, ['Revenue', number_format($revenue, 2, '.', '')]);
            fputcsv($output, ['Refunds', number_format($refunds, 2, '.', '')]);
            fputcsv($output, ['Expenses', number_format($expenses, 2, '.', '')]);
            fputcsv($output, ['Commissions', number_format($commissions, 2, '.', '')]);
            fputcsv($output, ['Campaign spend', number_format($campaignSpend, 2, '.', '')]);
            fputcsv($output, ['Net result', number_format($netResult, 2, '.', '')]);
            fputcsv($output, []);
            fputcsv($output, ['Expense detail']);
            fputcsv($output, ['Date', 'Description', 'Category', 'Status', 'Amount']);

            foreach ($expenseRows as $expense) {
                fputcsv($output, [
                    $expense->expense_date?->format('Y-m-d'),
                    $expense->description,
                    $expense->category,
                    $expense->status,
                    number_format($expense->amount, 2, '.', ''),
                ]);
            }

            fclose($output);
        }, 'financial-report-' . $from . '-to-' . $to . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
