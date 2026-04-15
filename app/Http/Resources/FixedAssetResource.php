<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'purchase_cost' => $this->purchase_cost,
            'lifespan_months' => $this->lifespan_months,
            'salvage_value' => $this->salvage_value,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
