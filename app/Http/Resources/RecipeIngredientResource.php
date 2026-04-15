<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeIngredientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ingredient_id' => $this->ingredient_id,
            'ingredient_name' => $this->whenLoaded('ingredient', fn () => $this->ingredient->name),
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'unit_name' => $this->whenLoaded('unit', fn () => $this->unit->name),
        ];
    }
}
