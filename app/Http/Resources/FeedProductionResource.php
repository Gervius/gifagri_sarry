<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedProductionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'recipe_id' => $this->recipe_id,
            'recipe_name' => $this->whenLoaded('recipe', fn () => $this->recipe->name),
            'quantity_produced' => $this->quantity_produced,
            'unit_id' => $this->unit_id,
            'production_date' => $this->production_date?->toDateString(),
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'permissions' => [
                'can_approve' => $request->user()?->can('approve', $this->resource) ?? false,
                'can_reject' => $request->user()?->can('reject', $this->resource) ?? false,
            ],
        ];
    }
}
