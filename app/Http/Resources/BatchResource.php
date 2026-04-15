<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'batch_number' => $this->batch_number,
            'manufacturing_date' => $this->manufacturing_date?->toDateString(),
            'expiration_date' => $this->expiration_date?->toDateString(),
            'initial_quantity' => $this->initial_quantity,
            'current_quantity' => $this->current_quantity,
            'batchable_type' => $this->batchable_type,
            'batchable_id' => $this->batchable_id,
            'batchable_name' => $this->whenLoaded('batchable', fn () => $this->batchable->name ?? null),
        ];
    }
}
