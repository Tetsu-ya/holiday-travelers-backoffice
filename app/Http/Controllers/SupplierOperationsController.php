<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierAvailability;
use App\Models\SupplierContract;
use App\Models\SupplierPerformance;
use App\Models\SupplierRate;
use Illuminate\Http\Request;

class SupplierOperationsController extends Controller
{
    private function suppliers()
    {
        return Supplier::orderBy('name')->get();
    }

    public function contracts()
    {
        return view('supplier-operations.index', ['module' => 'contracts', 'suppliers' => $this->suppliers(), 'records' => SupplierContract::with('supplier')->latest()->paginate(15)]);
    }

    public function storeContract(Request $request)
    {
        SupplierContract::create($request->validate(['supplier_id' => 'required|exists:suppliers,id', 'contract_number' => 'required|string|max:100', 'starts_on' => 'required|date', 'ends_on' => 'nullable|date|after_or_equal:starts_on', 'value' => 'required|numeric|min:0', 'status' => 'required|in:active,expired,draft', 'notes' => 'nullable|string']));
        return back()->with('success', 'Supplier contract added.');
    }

    public function destroyContract(SupplierContract $contract)
    {
        $contract->delete();

        return back()->with('success', 'Supplier contract removed.');
    }

    public function rates()
    {
        return view('supplier-operations.index', ['module' => 'rates', 'suppliers' => $this->suppliers(), 'records' => SupplierRate::with('supplier')->latest()->paginate(15)]);
    }

    public function storeRate(Request $request)
    {
        SupplierRate::create($request->validate(['supplier_id' => 'required|exists:suppliers,id', 'service' => 'required|string|max:255', 'rate' => 'required|numeric|min:0', 'unit' => 'required|string|max:80', 'effective_from' => 'required|date', 'effective_until' => 'nullable|date|after_or_equal:effective_from']));
        return back()->with('success', 'Supplier rate added.');
    }

    public function availability()
    {
        return view('supplier-operations.index', ['module' => 'availability', 'suppliers' => $this->suppliers(), 'records' => SupplierAvailability::with('supplier')->orderBy('available_on')->paginate(15)]);
    }

    public function storeAvailability(Request $request)
    {
        SupplierAvailability::create($request->validate(['supplier_id' => 'required|exists:suppliers,id', 'available_on' => 'required|date', 'capacity' => 'required|integer|min:0', 'status' => 'required|in:available,limited,unavailable', 'notes' => 'nullable|string']));
        return back()->with('success', 'Supplier availability added.');
    }

    public function destroyAvailability(SupplierAvailability $availability)
    {
        $availability->delete();

        return back()->with('success', 'Supplier availability removed.');
    }

    public function performance()
    {
        return view('supplier-operations.index', ['module' => 'performance', 'suppliers' => $this->suppliers(), 'records' => SupplierPerformance::with('supplier')->latest()->paginate(15)]);
    }

    public function storePerformance(Request $request)
    {
        SupplierPerformance::create($request->validate(['supplier_id' => 'required|exists:suppliers,id', 'period' => 'required|string|max:40', 'score' => 'required|numeric|min:0|max:100', 'bookings_completed' => 'required|integer|min:0', 'notes' => 'nullable|string']));
        return back()->with('success', 'Supplier performance recorded.');
    }

    public function destroyPerformance(SupplierPerformance $performance)
    {
        $performance->delete();

        return back()->with('success', 'Supplier performance removed.');
    }
}
