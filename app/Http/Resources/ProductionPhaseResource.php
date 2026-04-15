<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductionPhaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'animal_type_id' => $this->animal_type_id,
            'name' => $this->name,
            'typical_duration_days' => $this->typical_duration_days,
            'order' => $this->order,
            'default_recipe_id' => $this->default_recipe_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'animal_type_name' => $this->whenLoaded('animalType', fn () => $this->animalType->name),
        ];
    }
}
