<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledTreatmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'flock_id' => $this->flock_id,
            'scheduled_date' => $this->scheduled_date?->format('Y-m-d'),
            'planned_treatment_name' => $this->prophylaxisStep?->treatment_type,
            'status' => $this->status,
            'alert_days_before' => $this->alert_days_before,
            'actual_treatment_id' => $this->actual_treatment_id,
            'permissions' => [
                'can_mark_as_done' => $request->user()?->can('markAsDone', $this->resource) ?? false,
            ],
        ];
    }
}
