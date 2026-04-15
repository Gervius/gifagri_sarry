<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BreedResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'animal_type_id' => $this->animal_type_id,
            'animal_type' => $this->whenLoaded('animalType', fn () => [
                'id' => $this->animalType->id,
                'name' => $this->animalType->name,
                'code' => $this->animalType->code,
            ]),
            'can_edit' => $request->user()?->hasPermissionTo('update breeds') ?? false,
            'can_delete' => $request->user()?->hasPermissionTo('delete breeds') ?? false,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
