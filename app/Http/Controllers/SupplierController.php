<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(15);
        $supplierStats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('status', 'active')->count(),
            'average_reliability' => (Supplier::avg('reliability_rating') ?? 0) * 20,
        ];

        return view('suppliers.index', compact('suppliers', 'supplierStats'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:hotel,airline,transport,tour_guide,other',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'base_rate' => 'nullable|numeric|min:0',
            'reliability_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier added.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:hotel,airline,transport,tour_guide,other',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'base_rate' => 'nullable|numeric|min:0',
            'reliability_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}
