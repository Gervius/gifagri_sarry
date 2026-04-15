<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'amount' => $this->amount,
            'method' => $this->method,
            'reference' => $this->reference,
            'bank_account_name' => $this->bankAccount?->name,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
