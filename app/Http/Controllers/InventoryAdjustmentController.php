<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryAdjustmentRequest;
use App\Http\Resources\InventoryAdjustmentResource;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class InventoryAdjustmentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $adjustments = InventoryAdjustment::with('stockable')
            ->orderByDesc('created_at')
            ->get();

        return InventoryAdjustmentResource::collection($adjustments);
    }

    public function store(StoreInventoryAdjustmentRequest $request): InventoryAdjustmentResource
    {
        $adjustment = InventoryAdjustment::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]));

        return new InventoryAdjustmentResource($adjustment->load('stockable'));
    }

    public function show(InventoryAdjustment $inventoryAdjustment): InventoryAdjustmentResource
    {
        return new InventoryAdjustmentResource($inventoryAdjustment->load('stockable'));
    }

    public function update(StoreInventoryAdjustmentRequest $request, InventoryAdjustment $inventoryAdjustment): InventoryAdjustmentResource
    {
        $inventoryAdjustment->update($request->validated());

        return new InventoryAdjustmentResource($inventoryAdjustment->refresh()->load('stockable'));
    }

    public function approve(InventoryAdjustment $inventoryAdjustment): InventoryAdjustmentResource
    {
        if ($inventoryAdjustment->approved_at !== null) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Ajustement déjà approuvé.');
        }

        $inventoryAdjustment->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return new InventoryAdjustmentResource($inventoryAdjustment->refresh()->load('stockable'));
    }

    public function destroy(InventoryAdjustment $inventoryAdjustment): Response
    {
        $inventoryAdjustment->delete();

        return response()->noContent();
    }
}
