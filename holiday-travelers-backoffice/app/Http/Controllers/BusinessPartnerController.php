<?php

namespace App\Http\Controllers;

use App\Models\BusinessPartner;
use Illuminate\Http\Request;

class BusinessPartnerController extends Controller
{
    public function index()
    {
        $partners = BusinessPartner::latest()->paginate(15);
        $partnerStats = [
            'total' => BusinessPartner::count(),
            'top_region' => BusinessPartner::whereNotNull('region')
                ->select('region')
                ->groupBy('region')
                ->orderByRaw('COUNT(*) DESC')
                ->value('region') ?? 'N/A',
            'average_commission' => BusinessPartner::avg('commission_rate') ?? 0,
            'active' => BusinessPartner::where('status', 'active')->count(),
        ];

        return view('partners.index', compact('partners', 'partnerStats'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:agency,corporate,affiliate',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        BusinessPartner::create($data);
        return redirect()->route('partners.index')->with('success', 'Business partner added.');
    }

    public function show(BusinessPartner $partner)
    {
        return view('partners.show', compact('partner'));
    }

    public function edit(BusinessPartner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, BusinessPartner $partner)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:agency,corporate,affiliate',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $partner->update($data);
        return redirect()->route('partners.index')->with('success', 'Business partner updated.');
    }

    public function destroy(BusinessPartner $partner)
    {
        $partner->delete();
        return redirect()->route('partners.index')->with('success', 'Business partner deleted.');
    }
}
