<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Document;
use App\Models\VisaApplication;
use App\Models\VisaRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentOperationsController extends Controller
{
    public function documents()
    {
        return view('document-operations.index', ['module' => 'documents', 'records' => Document::where('related_type', Booking::class)->with(['uploadedBy', 'related'])->latest()->paginate(15), 'bookings' => Booking::latest('travel_date')->get()]);
    }

    public function storeDocument(Request $request)
    {
        $data = $request->validate(['booking_id' => 'required|exists:bookings,id', 'document_type' => 'required|string|max:100', 'document' => 'required|file|max:10240', 'expires_on' => 'nullable|date', 'status' => 'required|in:submitted,verified,rejected,expired']);
        $file = $request->file('document');
        $data['related_type'] = Booking::class; $data['related_id'] = $data['booking_id'];
        $data['file_name'] = $file->getClientOriginalName(); $data['file_path'] = $file->store('customer-documents');
        $data['uploaded_by'] = $request->user()->id; unset($data['booking_id'], $data['document']);
        Document::create($data);
        return back()->with('success', 'Customer document uploaded.');
    }

    public function applications()
    {
        return view('document-operations.index', ['module' => 'applications', 'records' => VisaApplication::with('booking')->latest()->paginate(15), 'bookings' => Booking::latest('travel_date')->get()]);
    }

    public function storeApplication(Request $request)
    {
        VisaApplication::create($request->validate(['booking_id' => 'nullable|exists:bookings,id', 'customer_name' => 'required|string|max:255', 'country' => 'required|string|max:100', 'visa_type' => 'required|string|max:100', 'travel_date' => 'nullable|date', 'submitted_on' => 'nullable|date', 'status' => 'required|in:draft,submitted,processing,approved,rejected', 'notes' => 'nullable|string']));
        return back()->with('success', 'Visa application added.');
    }

    public function requirements()
    {
        return view('document-operations.index', ['module' => 'requirements', 'records' => VisaRequirement::latest()->paginate(15)]);
    }

    public function storeRequirement(Request $request)
    {
        VisaRequirement::create($request->validate(['country' => 'required|string|max:100', 'visa_type' => 'required|string|max:100', 'document_name' => 'required|string|max:255', 'description' => 'nullable|string', 'required' => 'nullable|boolean']));
        return back()->with('success', 'Visa requirement added.');
    }

    public function checklist()
    {
        return view('document-operations.index', ['module' => 'checklist', 'records' => VisaRequirement::orderBy('country')->orderBy('visa_type')->paginate(20)]);
    }

    public function expiration()
    {
        $records = Document::whereNotNull('expires_on')->where('expires_on', '<=', now()->addDays(90))->with('related')->orderBy('expires_on')->paginate(15);
        return view('document-operations.index', ['module' => 'expiration', 'records' => $records]);
    }

    public function status()
    {
        return view('document-operations.index', ['module' => 'status', 'records' => VisaApplication::latest()->paginate(15)]);
    }
}
