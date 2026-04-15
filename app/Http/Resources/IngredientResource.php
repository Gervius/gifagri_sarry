<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'reference' => $this->reference,
            'default_unit_id' => $this->default_unit_id,
            'current_stock' => number_format((float) $this->current_stock, 2, '.', ''),
            'pmp' => number_format((float) $this->pmp, 2, '.', ''),
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'is_active' => $this->is_active,
            'partner_id' => $this->partner_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'permissions' => [
                'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
            ],
        ];
    }
}
