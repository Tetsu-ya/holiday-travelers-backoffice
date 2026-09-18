<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::latest()->paginate(15);
        return view('packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:domestic,international',
            'destination' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'slots' => 'nullable|integer|min:0',
        ]);

        TourPackage::create($data);
        return redirect()->route('packages.index')->with('success', 'Tour package added.');
    }

    public function show(TourPackage $package)
    {
        return view('packages.show', compact('package'));
    }

    public function edit(TourPackage $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, TourPackage $package)
    {
        $package->update($request->all());
        return redirect()->route('packages.index')->with('success', 'Tour package updated.');
    }

    public function destroy(TourPackage $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Tour package deleted.');
    }

    public function create()
    {
        return view('packages.create');
    }
}