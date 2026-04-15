<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountingRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'event_type' => $this->event_type,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'accounting_rule_lines' => $this->whenLoaded('accountingRuleLines', fn () => $this->accountingRuleLines->map(fn ($line) => [
                'id' => $line->id,
                'type' => $line->type,
                'account_resolution_type' => $line->account_resolution_type,
                'account_id' => $line->account_id,
                'amount_source' => $line->amount_source,
                'percentage' => $line->percentage,
            ])),
        ];
    }
}
