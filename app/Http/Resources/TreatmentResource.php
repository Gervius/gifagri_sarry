<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreatmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'flock_id' => $this->flock_id,
            'batch_id' => $this->batch_id,
            'treatment_date' => $this->treatment_date?->toDateString(),
            'status' => $this->status,
            'treatment_type' => $this->treatment_type,
            'veterinarian' => $this->veterinarian,
            'description' => $this->description,
            'cost' => $this->cost,
            'invoice_reference' => $this->invoice_reference,
            'rejection_reason' => $this->rejection_reason,
            'approved_at' => $this->approved_at?->toDateTimeString(),
            'batch_number' => $this->whenLoaded('batch', fn () => $this->batch->batch_number),
            'flock_name' => $this->whenLoaded('flock', fn () => $this->flock->name),
            'permissions' => [
                'can_approve' => $request->user()?->can('approve', $this->resource) ?? false,
                'can_reject' => $request->user()?->can('reject', $this->resource) ?? false,
                'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
            ],
        ];
    }
}
