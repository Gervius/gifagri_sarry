<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'stock_quantity' => $this->stock_quantity,
            'unit_name' => $this->whenLoaded('unit', fn () => $this->unit->name),
            'accounting_account_id' => $this->accounting_account_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'permissions' => [
                'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
            ],
        ];
    }
}
