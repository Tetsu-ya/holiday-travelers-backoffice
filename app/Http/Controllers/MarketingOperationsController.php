<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use App\Models\MarketingBrochure;
use App\Models\MarketingCampaign;
use App\Models\Promotion;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class MarketingOperationsController extends Controller
{
    public function promotions()
    {
        return view('marketing-operations.index', ['module' => 'promotions', 'records' => Promotion::latest()->paginate(15)]);
    }

    public function storePromotion(Request $request)
    {
        Promotion::create($request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string', 'discount_type' => 'required|in:percentage,fixed', 'discount_value' => 'required|numeric|min:0', 'starts_on' => 'required|date', 'ends_on' => 'required|date|after_or_equal:starts_on', 'status' => 'required|in:draft,active,expired']));
        return back()->with('success', 'Promotion added.');
    }

    public function discountCodes()
    {
        return view('marketing-operations.index', ['module' => 'discount-codes', 'records' => DiscountCode::latest()->paginate(15)]);
    }

    public function storeDiscountCode(Request $request)
    {
        DiscountCode::create($request->validate(['code' => 'required|string|max:50|unique:discount_codes,code', 'description' => 'nullable|string', 'discount_type' => 'required|in:percentage,fixed', 'discount_value' => 'required|numeric|min:0', 'usage_limit' => 'nullable|integer|min:1', 'starts_on' => 'required|date', 'ends_on' => 'required|date|after_or_equal:starts_on', 'status' => 'required|in:active,inactive,expired']));
        return back()->with('success', 'Discount code added.');
    }

    public function calendar()
    {
        $campaigns = MarketingCampaign::orderBy('start_date')->paginate(15);
        return view('marketing-operations.index', ['module' => 'calendar', 'records' => $campaigns]);
    }

    public function analytics()
    {
        $campaigns = MarketingCampaign::whereIn('channel', ['facebook', 'instagram', 'tiktok', 'social_media'])
            ->with('brochure')
            ->latest()
            ->paginate(10, ['*'], 'campaign_page');
        $brochures = MarketingBrochure::latest()->paginate(9, ['*'], 'brochure_page');
        $packages = TourPackage::latest()->take(6)->get();
        $analytics = [
            'leads' => (int) MarketingCampaign::whereIn('channel', ['facebook', 'instagram', 'tiktok', 'social_media'])->sum('leads_generated'),
            'conversions' => (int) MarketingCampaign::whereIn('channel', ['facebook', 'instagram', 'tiktok', 'social_media'])->sum('conversions'),
            'byChannel' => MarketingCampaign::selectRaw('channel, SUM(conversions) as conversions')
                ->whereIn('channel', ['facebook', 'instagram', 'tiktok', 'social_media'])
                ->groupBy('channel')
                ->orderByDesc('conversions')
                ->get(),
        ];

        return view('marketing-hub.index', compact('campaigns', 'brochures', 'packages', 'analytics'));
    }

    public function storeBrochure(Request $request)
    {
        $file = $request->file('brochure');
        if ($file instanceof UploadedFile && !$file->isValid()) {
            $errorCode = $file->getError();
            $message = match ($errorCode) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The web server rejected this file because it exceeds the configured upload limit. Restart Apache to load the updated PHP settings, then try again.',
                UPLOAD_ERR_PARTIAL => 'The image upload was interrupted before it finished. Please choose the file again and retry.',
                UPLOAD_ERR_NO_TMP_DIR => 'PHP cannot find its temporary upload folder. Check the upload_tmp_dir setting in php.ini.',
                UPLOAD_ERR_CANT_WRITE => 'PHP could not write the uploaded file to its temporary folder. Check that folder’s permissions and available disk space.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the image upload. Check the Apache PHP error log.',
                default => 'The image upload failed before the application received it. Please retry; if it continues, check the Apache PHP error log.',
            };

            Log::warning('Marketing brochure upload rejected by PHP.', [
                'upload_error' => $errorCode,
                'upload_error_message' => $file->getErrorMessage(),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            ]);

            return redirect()->route('campaign-analytics.index', ['tab' => 'brochures'])
                ->withErrors(['brochure' => $message])
                ->withInput($request->except('brochure'));
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'brochure' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'brochure.uploaded' => 'The image could not be received by PHP. Please retry; if it continues, check the Apache PHP error log.',
            'brochure.max' => 'The brochure or photo must not be larger than 10 MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('campaign-analytics.index', ['tab' => 'brochures'])
                ->withErrors($validator)
                ->withInput($request->except('brochure'));
        }

        $data = $validator->validated();
        $path = $file->store('marketing-brochures', 'local');
        if ($path === false) {
            throw new \RuntimeException('The brochure could not be saved to local storage.');
        }

        try {
            MarketingBrochure::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return redirect()->route('campaign-analytics.index', ['tab' => 'brochures'])
            ->with('success', 'Promotional brochure uploaded.');
    }

    public function downloadBrochure(MarketingBrochure $brochure)
    {
        abort_unless(Storage::disk('local')->exists($brochure->file_path), 404, 'The brochure file could not be found.');

        return Storage::disk('local')->download($brochure->file_path, $brochure->file_name);
    }

    public function previewBrochure(MarketingBrochure $brochure)
    {
        abort_unless(in_array($brochure->mime_type, ['image/jpeg', 'image/png'], true), 404);
        abort_unless(Storage::disk('local')->exists($brochure->file_path), 404, 'The brochure image could not be found.');

        return Storage::disk('local')->response($brochure->file_path, $brochure->file_name, [
            'Content-Type' => $brochure->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroyBrochure(MarketingBrochure $brochure)
    {
        Storage::disk('local')->delete($brochure->file_path);
        $brochure->delete();

        return redirect()->route('campaign-analytics.index', ['tab' => 'brochures'])
            ->with('success', 'Promotional brochure deleted.');
    }

}
