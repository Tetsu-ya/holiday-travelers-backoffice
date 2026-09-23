<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class SyncBookingPaymentStatus extends Command
{
    protected $signature = 'bookings:sync-payment-status';

    protected $description = 'Align every booking\'s payment status (and cancelled/refunded booking status) with its payment rows';

    public function handle(): int
    {
        $updated = 0;

        Booking::with('payments')->chunkById(100, function ($bookings) use (&$updated) {
            foreach ($bookings as $booking) {
                $receivedTotal = (float) $booking->payments->whereIn('status', ['paid', 'partial'])->sum('amount');
                $refundedTotal = (float) $booking->payments->where('status', 'refunded')->sum('amount');
                $cancelledTotal = (float) $booking->payments->where('status', 'cancelled')->sum('amount');

                if ($receivedTotal > 0) {
                    $paymentStatus = $receivedTotal >= (float) $booking->total_amount ? 'paid' : 'partial';
                } elseif ($refundedTotal > 0) {
                    $paymentStatus = 'refunded';
                } elseif ($cancelledTotal > 0) {
                    $paymentStatus = 'cancelled';
                } else {
                    $paymentStatus = 'unpaid';
                }

                $status = $booking->status;
                if (in_array($paymentStatus, ['refunded', 'cancelled'], true)) {
                    $status = $paymentStatus;
                } elseif ($receivedTotal > 0) {
                    $status = 'confirmed';
                }

                if ($booking->payment_status !== $paymentStatus || $booking->status !== $status) {
                    $booking->payment_status = $paymentStatus;
                    $booking->status = $status;
                    $booking->save();
                    $updated++;
                }
            }
        });

        $this->info("Synced payment status for {$updated} booking(s).");

        return self::SUCCESS;
    }
}