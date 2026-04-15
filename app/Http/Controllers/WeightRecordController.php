<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreWeightRecordRequest;
use App\Http\Resources\WeightRecordResource;
use App\Models\WeightRecord;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class WeightRecordController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $weightRecords = WeightRecord::orderByDesc('date')->get();

        return WeightRecordResource::collection($weightRecords);
    }

    public function store(StoreWeightRecordRequest $request): WeightRecordResource
    {
        $weightRecord = WeightRecord::create($request->validated());

        return new WeightRecordResource($weightRecord);
    }

    public function show(WeightRecord $weightRecord): WeightRecordResource
    {
        return new WeightRecordResource($weightRecord);
    }

    public function update(StoreWeightRecordRequest $request, WeightRecord $weightRecord): WeightRecordResource
    {
        $weightRecord->update($request->validated());

        return new WeightRecordResource($weightRecord);
    }

    public function destroy(WeightRecord $weightRecord): Response
    {
        $weightRecord->delete();

        return response()->noContent();
    }
}
