<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'customer_name' => $this->customer_name ?: $this->whenLoaded('partner', fn () => $this->partner->name),
            'total_ttc' => $this->total,
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'permissions' => [
                'can_approve' => $request->user()?->can('approve', $this->resource) ?? false,
                'can_cancel' => $request->user()?->can('cancel', $this->resource) ?? false,
            ],
        ];
    }
}
