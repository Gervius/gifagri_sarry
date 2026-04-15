<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'yield' => $this->yield,
            'unit_id' => $this->unit_id,
            'animal_type_name' => $this->whenLoaded('animalType', fn () => $this->animalType->name),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'ingredients' => RecipeIngredientResource::collection($this->whenLoaded('recipeIngredients')),
            'permissions' => [
                'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
            ],
        ];
    }
}
