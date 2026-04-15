<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnimalTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'can_lay_eggs' => $this->can_lay_eggs,
            'has_growth_tracking' => $this->has_growth_tracking,
            'has_breeding_cycle' => $this->has_breeding_cycle,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
