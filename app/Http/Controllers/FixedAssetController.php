<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\FixedAssetResource;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FixedAssetController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $assets = FixedAsset::orderBy('purchase_date')->get();

        return FixedAssetResource::collection($assets);
    }

    public function store(Request $request): FixedAssetResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'purchase_cost' => ['required', 'numeric', 'gt:0'],
            'lifespan_months' => ['required', 'integer', 'min:1'],
            'salvage_value' => ['required', 'numeric', 'min:0'],
            'asset_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'depreciation_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'expense_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        $asset = FixedAsset::create($validated);

        return new FixedAssetResource($asset);
    }

    public function show(FixedAsset $fixedAsset): FixedAssetResource
    {
        return new FixedAssetResource($fixedAsset);
    }

    public function update(Request $request, FixedAsset $fixedAsset): FixedAssetResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date'],
            'purchase_cost' => ['required', 'numeric', 'gt:0'],
            'lifespan_months' => ['required', 'integer', 'min:1'],
            'salvage_value' => ['required', 'numeric', 'min:0'],
            'asset_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'depreciation_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'expense_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ]);

        $fixedAsset->update($validated);

        return new FixedAssetResource($fixedAsset->refresh());
    }

    public function destroy(FixedAsset $fixedAsset): Response
    {
        $fixedAsset->delete();

        return response()->noContent();
    }
}
