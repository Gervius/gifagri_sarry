<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'type_label' => $this->translateType($this->type),
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'status' => $this->status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'approved_at' => $this->approved_at?->toDateTimeString(),
            'ingredient_name' => $this->whenLoaded('ingredient', fn () => $this->ingredient->name),
            'batch_number' => $this->whenLoaded('batch', fn () => $this->batch->number),
            'permissions' => [
                'can_approve' => $request->user()?->can('approve', $this->resource) ?? false,
            ],
        ];
    }

    private function translateType(string $type): string
    {
        return match ($type) {
            'in' => 'Entrée',
            'out' => 'Sortie',
            'adjust' => 'Ajustement',
            default => 'Inconnu',
        };
    }
}
