<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource pour les balances partenaires
 */
class PartnerBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'partner_id' => $this->partner_id,
            'partner_name' => $this->partner_name,
            'partner_type' => $this->partner_type,
            'receivables_balance' => $this->receivables_balance,
            'payables_balance' => $this->payables_balance,
        ];
    }
}