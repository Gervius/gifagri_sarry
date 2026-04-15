<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'delivery_number' => $this->delivery_number,
            'date' => $this->date?->format('Y-m-d'),
            'partner_name' => $this->partner?->name,
            'invoice_number' => $this->invoice?->number,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
