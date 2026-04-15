<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource pour les factures en retard
 */
class OverdueInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'partner' => [
                'id' => $this->partner->id ?? null,
                'name' => $this->partner->name ?? $this->customer_name,
            ],
            'date' => $this->date,
            'due_date' => $this->due_date,
            'total' => $this->total,
            'payment_status' => $this->payment_status,
            'paid_amount' => $this->payments->sum('amount'),
            'remaining_balance' => $this->total - $this->payments->sum('amount'),
            'days_overdue' => now()->diffInDays($this->due_date),
        ];
    }
}