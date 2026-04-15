<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'type' => $this->type,
            'conversion_factor' => $this->conversion_factor,
            'base_unit_id' => $this->base_unit_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'base_unit_name' => $this->whenLoaded('baseUnit', fn () => $this->baseUnit->name),
        ];
    }
}
