<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryAdjustmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->created_at?->format('Y-m-d'),
            'stockable_entity' => $this->whenLoaded('stockable', function () {
                return $this->stockable->name ?? class_basename($this->stockable);
            }),
            'stockable_type' => class_basename($this->stockable_type),
            'expected_quantity' => $this->expected_quantity,
            'actual_quantity' => $this->actual_quantity,
            'reason' => $this->reason,
            'approved_at' => $this->approved_at?->toDateTimeString(),
        ];
    }
}
