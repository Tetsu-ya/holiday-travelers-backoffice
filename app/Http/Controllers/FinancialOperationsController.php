<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Support\Revenue;

class FinancialOperationsController extends Controller
{
    public function revenue()
    {
        $revenue = Revenue::collected();
        $payments = Payment::with('booking')->whereIn('status', Revenue::RECEIVED)->latest('paid_at')->paginate(15);

        return view('financial-operations.index', compact('payments', 'revenue') + [
            'refunds' => Revenue::refunded(),
            'outstanding' => Revenue::outstanding(),
        ]);
    }
}
