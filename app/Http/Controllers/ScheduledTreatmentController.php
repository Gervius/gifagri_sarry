<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CompleteScheduledTreatmentRequest;
use App\Http\Resources\ScheduledTreatmentResource;
use App\Models\ScheduledTreatment;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScheduledTreatmentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $scheduledTreatments = ScheduledTreatment::with('prophylaxisStep')
            ->orderBy('scheduled_date')
            ->get();

        return ScheduledTreatmentResource::collection($scheduledTreatments);
    }

    public function markAsDone(CompleteScheduledTreatmentRequest $request, ScheduledTreatment $scheduledTreatment): ScheduledTreatmentResource
    {
        $data = ['status' => 'done'];

        if ($request->filled('actual_treatment_id')) {
            $data['actual_treatment_id'] = $request->input('actual_treatment_id');
        }

        $scheduledTreatment->update($data);

        return new ScheduledTreatmentResource($scheduledTreatment->refresh()->load('prophylaxisStep'));
    }
}
