<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BusinessPartner;
use App\Models\MarketingCampaign;
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
}