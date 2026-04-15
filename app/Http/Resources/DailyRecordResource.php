<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyRecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->toDateString(),
            'status' => $this->status,
            'losses' => $this->losses,
            'eggs' => $this->eggs,
            'feed_consumed' => $this->feed_consumed,
            'water_consumed' => $this->water_consumed,
            'feed_type_name' => $this->whenLoaded('feedType', fn () => $this->feedType->name),
            'permissions' => [
                'can_approve' => $request->user()?->can('approve', $this->resource) ?? false,
                'can_reject' => $request->user()?->can('reject', $this->resource) ?? false,
            ],
        ];
    }
}
