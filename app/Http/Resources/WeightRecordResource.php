<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeightRecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'flock_id' => $this->flock_id,
            'date' => $this->date?->format('Y-m-d'),
            'average_weight' => $this->average_weight,
            'sample_size' => $this->sample_size,
        ];
    }
}
