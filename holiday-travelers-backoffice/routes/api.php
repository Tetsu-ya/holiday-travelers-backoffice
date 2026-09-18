<?php

use App\Http\Controllers\Api\BookingApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());

    Route::apiResource('bookings', BookingApiController::class)->only(['index', 'store', 'show']);
    Route::apiResource('partners', \App\Http\Controllers\Api\BusinessPartnerApiController::class)->only(['index', 'show']);
    Route::apiResource('suppliers', \App\Http\Controllers\Api\SupplierApiController::class)->only(['index', 'show']);
    Route::get('ai-plans', [\App\Http\Controllers\Api\AiResourcePlanApiController::class, 'index']);
});
