<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProphylaxisPlanRequest;
use App\Http\Resources\ProphylaxisPlanResource;
use App\Models\ProphylaxisPlan;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProphylaxisPlanController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $plans = ProphylaxisPlan::with('steps')->get();

        return ProphylaxisPlanResource::collection($plans);
    }

    public function store(StoreProphylaxisPlanRequest $request): ProphylaxisPlanResource
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, &$plan): void {
            $plan = ProphylaxisPlan::create(Arr::except($validated, ['steps']));
            $plan->steps()->createMany($validated['steps']);
        });

        return new ProphylaxisPlanResource($plan->load('steps'));
    }

    public function show(ProphylaxisPlan $prophylaxisPlan): ProphylaxisPlanResource
    {
        return new ProphylaxisPlanResource($prophylaxisPlan->load('steps'));
    }
}
