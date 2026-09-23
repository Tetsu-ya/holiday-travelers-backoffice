<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Collection;

/**
 * Single source of truth for money figures.
 *
 * Basis: CASH — money is recognized in the month it was actually received
 * (payments.paid_at), not the month the trip departs. Refunds are subtracted
 * from revenue rather than reported alongside it.
 */
class Revenue
{
    /** Payment rows that represent money actually received. */
    public const RECEIVED = ['paid', 'partial'];

    /** Bookings that represent committed business. */
    public const COMMITTED = ['confirmed', 'completed'];

    /**
     * Total cash received, net of refunds.
     */
    public static function collected(): float
    {
        return round(self::grossCollected() - self::refunded(), 2);
    }

    /** Cash received before refunds. */
    public static function grossCollected(): float
    {
        return (float) Payment::whereIn('status', self::RECEIVED)->sum('amount');
    }

    /** Cash returned to customers. */
    public static function refunded(): float
    {
        return (float) Payment::where('status', 'refunded')->sum('amount');
    }

    /** Cash received in a specific month of a specific year. */
    public static function collectedForMonth(int $month, ?int $year = null): float
    {
        $year ??= (int) now()->year;

        $gross = (float) Payment::whereIn('status', self::RECEIVED)
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->sum('amount');

        $refunded = (float) Payment::where('status', 'refunded')
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->sum('amount');

        return round($gross - $refunded, 2);
    }

    /**
     * Cash received per month for a full year, keyed 1-12.
     */
    public static function monthlyCollected(?int $year = null): Collection
    {
        $year ??= (int) now()->year;

        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => self::collectedForMonth($month, $year)]);
    }

    /**
     * Cash received per month for a full year, keyed 1-12, grouped by the
     * month the related booking TRAVELS (bookings.travel_date) rather than the
     * month the payment landed.
     *
     * Payments are deducted when refunded, so this mirrors the cash-basis
     * "collected" figure — just organised by travel month.
     */
    public static function monthlyCollectedByTravelMonth(?int $year = null): Collection
    {
        $year ??= (int) now()->year;

        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => self::collectedForTravelMonth($month, $year)]);
    }

    /**
     * Cash received (net of refunds) across bookings whose travel month is the
     * given month of the given year.
     */
    public static function collectedForTravelMonth(int $month, ?int $year = null): float
    {
        $year ??= (int) now()->year;

        $gross = (float) Payment::whereIn('status', self::RECEIVED)
            ->whereHas('booking', fn ($booking) => $booking
                ->whereYear('travel_date', $year)
                ->whereMonth('travel_date', $month))
            ->sum('amount');

        $refunded = (float) Payment::where('status', 'refunded')
            ->whereHas('booking', fn ($booking) => $booking
                ->whereYear('travel_date', $year)
                ->whereMonth('travel_date', $month))
            ->sum('amount');

        return round($gross - $refunded, 2);
    }

    /**
     * Booked value of all committed bookings (accrual figure, for context —
     * not the same thing as cash collected).
     */
    public static function bookedValue(): float
    {
        return (float) Booking::whereIn('status', self::COMMITTED)->sum('total_amount');
    }

    /**
     * Total still owed across committed bookings whose payments fall short
     * of the booking amount.
     */
    public static function outstanding(): float
    {
        return round(Booking::whereIn('status', self::COMMITTED)
            ->withSum(['payments as received_amount' => fn ($query) => $query->whereIn('status', self::RECEIVED)], 'amount')
            ->get()
            ->sum(fn (Booking $booking) => max(
                0,
                (float) $booking->total_amount - (float) ($booking->received_amount ?? 0)
            )), 2);
    }
}
