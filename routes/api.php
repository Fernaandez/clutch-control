<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MotorcycleBrandController;

// Default user route
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ─── Motorcycle Brands & Models (public, no auth needed) ─────────────────────
Route::get('/motorcycle-brands', [MotorcycleBrandController::class, 'brands']);
Route::get('/motorcycle-models', [MotorcycleBrandController::class, 'models']);
Route::post('/motorcycle-brands/save-custom', [MotorcycleBrandController::class, 'saveCustom']);