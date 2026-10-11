<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Support\Revenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->selectedPeriod($request);
        $reportStats = $this->periodTotals($from, $to);
        $reportStats['monthlyRevenue'] = $this->monthlyRevenue($from, $to);
        $reportStats['paymentMethods'] = $this->paymentMethods($from, $to);
        $reportStats['paymentMethodGradient'] = $this->paymentMethodGradient($reportStats['paymentMethods']);
        $reportStats['paymentRecordTotal'] = array_sum(array_column($reportStats['paymentMethods'], 'amount'));
        $reportStats['paymentStatusSummary'] = $this->paymentStatusSummary($from, $to);
        $reportStats['transactions'] = $this->recentTransactions($from, $to);

        return view('reports.index', compact('reportStats', 'from', 'to'));
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

        $from = Carbon::parse($filters['from'])->startOfDay();
        $to = Carbon::parse($filters['to'])->endOfDay();
        $totals = $this->periodTotals($from, $to);
        $paymentStatuses = $this->paymentStatusSummary($from, $to);

        return response()->streamDownload(function () use ($from, $to, $totals, $paymentStatuses) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['Financial Report']);
            fputcsv($output, ['Period', $from->toDateString() . ' to ' . $to->toDateString()]);
            fputcsv($output, ['Revenue basis', 'Cash received by payment date, less refunds']);
            fputcsv($output, []);
            fputcsv($output, ['Metric', 'Amount']);
            fputcsv($output, ['Gross cash received', number_format($totals['grossRevenue'], 2, '.', '')]);
            fputcsv($output, ['Refunds', number_format($totals['refunds'], 2, '.', '')]);
            fputcsv($output, ['Net cash revenue', number_format($totals['revenue'], 2, '.', '')]);
            fputcsv($output, ['Bookings created', $totals['bookings']]);
            fputcsv($output, []);
            fputcsv($output, ['Payment status', 'Count', 'Recorded amount']);
            foreach ($paymentStatuses as $status) {
                fputcsv($output, [
                    $status['name'],
                    $status['count'],
                    number_format($status['amount'], 2, '.', ''),
                ]);
            }

            fclose($output);
        }, 'financial-report-' . $from->toDateString() . '-to-' . $to->toDateString() . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function selectedPeriod(Request $request): array
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $from = Carbon::parse($filters['from'] ?? now()->startOfYear()->toDateString())->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now()->toDateString())->endOfDay();

        if ($from->gt($to)) {
            throw ValidationException::withMessages([
                'to' => 'The end date must be on or after the start date.',
            ]);
        }

        return [$from, $to];
    }

    private function periodTotals(Carbon $from, Carbon $to): array
    {
        $receivedPayments = Payment::whereIn('status', Revenue::RECEIVED)
            ->whereBetween('paid_at', [$from, $to]);
        $grossRevenue = (float) (clone $receivedPayments)->sum('amount');
        $refunds = (float) Payment::where('status', 'refunded')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');
        $revenue = round($grossRevenue - $refunds, 2);
        return [
            'grossRevenue' => $grossRevenue,
            'refunds' => $refunds,
            'revenue' => $revenue,
            'bookings' => Booking::whereBetween('created_at', [$from, $to])->count(),
        ];
    }

    private function monthlyRevenue(Carbon $from, Carbon $to): array
    {
        $months = [];
        $month = $from->copy()->startOfMonth();

        while ($month->lte($to)) {
            $bucketFrom = $month->copy()->max($from);
            $bucketTo = $month->copy()->endOfMonth()->min($to);
            $gross = (float) Payment::whereIn('status', Revenue::RECEIVED)
                ->whereBetween('paid_at', [$bucketFrom, $bucketTo])
                ->sum('amount');
            $refunds = (float) Payment::where('status', 'refunded')
                ->whereBetween('paid_at', [$bucketFrom, $bucketTo])
                ->sum('amount');

            $months[] = [
                'label' => $month->format('M y'),
                'amount' => round($gross - $refunds, 2),
            ];
            $month->addMonth();
        }

        return $months;
    }

    private function paymentMethods(Carbon $from, Carbon $to): array
    {
        $colors = ['#2563eb', '#14b8a6', '#f59e0b', '#8b5cf6', '#64748b'];
        $methods = $this->paymentsInPeriod($from, $to)
            ->selectRaw('payment_method, SUM(amount) as amount')
            ->groupBy('payment_method')
            ->orderByDesc('amount')
            ->get();
        $total = (float) $methods->sum('amount');

        return $methods->values()->map(fn ($method, $index) => [
            'name' => ucfirst(str_replace('_', ' ', $method->payment_method)),
            'amount' => (float) $method->amount,
            'percentage' => $total > 0 ? ((float) $method->amount / $total) * 100 : 0,
            'color' => $colors[$index % count($colors)],
        ])->all();
    }

    private function paymentStatusSummary(Carbon $from, Carbon $to): array
    {
        $statusOrder = ['paid', 'partial', 'pending', 'cancelled', 'refunded'];
        $summary = $this->paymentsInPeriod($from, $to)
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return collect($statusOrder)->map(fn (string $status) => [
            'name' => ucfirst($status),
            'count' => (int) ($summary->get($status)?->count ?? 0),
            'amount' => (float) ($summary->get($status)?->amount ?? 0),
        ])->all();
    }

    private function paymentsInPeriod(Carbon $from, Carbon $to)
    {
        return Payment::query()
            ->whereIn('status', ['paid', 'partial', 'pending', 'cancelled', 'refunded'])
            ->whereRaw('COALESCE(paid_at, created_at) BETWEEN ? AND ?', [$from, $to]);
    }

    private function paymentMethodGradient(array $methods): string
    {
        if ($methods === []) {
            return 'conic-gradient(#e5e7eb 0% 100%)';
        }

        $stops = [];
        $position = 0;
        foreach ($methods as $method) {
            $nextPosition = $position + $method['percentage'];
            $stops[] = $method['color'] . ' ' . $position . '% ' . $nextPosition . '%';
            $position = $nextPosition;
        }

        return 'conic-gradient(' . implode(', ', $stops) . ')';
    }

    private function recentTransactions(Carbon $from, Carbon $to): array
    {
        $payments = $this->paymentsInPeriod($from, $to)
            ->with('booking')
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->take(10)
            ->get()
            ->map(fn (Payment $payment) => [
                'date' => $payment->paid_at ?? $payment->created_at,
                'description' => 'Booking ' . ($payment->booking?->reference_no ?? 'payment'),
                'category' => $payment->status === 'refunded' ? 'Refund' : ucfirst($payment->status) . ' payment',
                'amount' => $payment->status === 'refunded' ? -(float) $payment->amount : (float) $payment->amount,
                'status' => $payment->status,
            ]);

        return $payments
            ->sortByDesc(fn (array $transaction) => $transaction['date']?->timestamp ?? 0)
            ->take(8)
            ->values()
            ->all();
    }
}
