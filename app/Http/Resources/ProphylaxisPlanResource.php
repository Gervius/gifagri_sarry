<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProphylaxisPlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'animal_type_id' => $this->animal_type_id,
            'description' => $this->description,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'steps' => $this->whenLoaded('steps', function () {
                return $this->steps->map(fn ($step) => [
                    'id' => $step->id,
                    'day_of_age' => $step->day_of_age,
                    'treatment_type' => $step->treatment_type,
                    'administration_method' => $step->administration_method,
                    'description' => $step->description,
                ]);
            }),
        ];
    }
}
