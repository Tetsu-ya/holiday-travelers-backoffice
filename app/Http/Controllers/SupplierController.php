<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $category = (string) $request->input('category', 'all');
        $type = (string) $request->input('type', '');
        $status = (string) $request->input('status', 'active');
        $categoryMap = [
            'hotels' => ['hotel'],
            'transportation' => ['transport', 'airline'],
            'tour-activities' => ['tour_guide'],
            'services' => ['other'],
        ];

        $query = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when(isset($categoryMap[$category]), fn ($query) => $query->whereIn('category', $categoryMap[$category]))
            ->when(in_array($type, ['hotel', 'transport', 'tour_guide', 'other'], true), function ($query) use ($type) {
                $query->where('category', $type === 'transport' ? 'transport' : $type);
            })
            ->when(in_array($status, ['active', 'inactive', 'blacklisted'], true), fn ($query) => $query->where('status', $status));

        $suppliers = $query->latest()->paginate(15)->withQueryString();
        $supplierStats = [
            'total' => Supplier::count(),
            'hotels' => Supplier::where('category', 'hotel')->count(),
            'transportation' => Supplier::whereIn('category', ['transport', 'airline'])->count(),
            'tour_activities' => Supplier::where('category', 'tour_guide')->count(),
            'services' => Supplier::where('category', 'other')->count(),
            'active' => Supplier::where('status', 'active')->count(),
            'average_reliability' => (Supplier::avg('reliability_rating') ?? 0) * 20,
        ];

        return view('suppliers.index', compact('suppliers', 'supplierStats', 'search', 'category', 'type', 'status'));
    }

    public function create(Request $request)
    {
        $category = $request->input('category', 'hotel');
        return view('suppliers.create', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:hotel,airline,transport,tour_guide,other',
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'base_rate' => 'nullable|numeric|min:0',
            'reliability_rating' => 'nullable|numeric|min:0|max:5',
            'agreement_valid_until' => 'nullable|date',
            'perks_inclusions' => 'nullable|string|max:500',
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
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'base_rate' => 'nullable|numeric|min:0',
            'reliability_rating' => 'nullable|numeric|min:0|max:5',
            'agreement_valid_until' => 'nullable|date',
            'perks_inclusions' => 'nullable|string|max:500',
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
