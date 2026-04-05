<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotorcycleBrandController extends Controller
{
    /**
     * GET /api/motorcycle-brands
     * Returns all brands ordered by name.
     */
    public function brands()
    {
        $brands = DB::table('motorcycle_brands')
            ->orderBy('name')
            ->get(['id', 'name', 'country']);

        return response()->json($brands);
    }

    /**
     * GET /api/motorcycle-models?brand_id=X
     * Returns models for a given brand, or saves a new one if it doesn't exist.
     */
    public function models(Request $request)
    {
        $brandId = $request->query('brand_id');

        if (!$brandId) {
            return response()->json([]);
        }

        $models = DB::table('motorcycle_models')
            ->where('brand_id', $brandId)
            ->orderBy('name')
            ->get(['id', 'name', 'cc', 'power_cv', 'type']);

        return response()->json($models);
    }

    /**
     * POST /api/motorcycle-brands/save-custom
     * Saves a custom brand+model if it doesn't exist yet.
     * This runs when the user types something that isn't in the DB.
     */
    public function saveCustom(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
        ]);

        $brandName = trim($request->brand);
        $modelName = trim($request->model);

        // Get or create the brand
        $brand = DB::table('motorcycle_brands')
            ->where('name', $brandName)
            ->first();

        if (!$brand) {
            $brandId = DB::table('motorcycle_brands')->insertGetId([
                'name'       => $brandName,
                'country'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $brandId = $brand->id;
        }

        // Check if model already exists for this brand
        $existingModel = DB::table('motorcycle_models')
            ->where('brand_id', $brandId)
            ->where('name', $modelName)
            ->first();

        if (!$existingModel) {
            DB::table('motorcycle_models')->insert([
                'brand_id'   => $brandId,
                'name'       => $modelName,
                'cc'         => null,
                'power_cv'   => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'brand_id'   => $brandId,
            'brand_name' => $brandName,
            'model_name' => $modelName,
        ]);
    }
}
