<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PigBreedingEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'flock_id' => $this->flock_id,
            'boar_flock_id' => $this->boar_flock_id,
            'event_type' => $this->event_type,
            'event_date' => $this->event_date?->toDateString(),
            'piglets_born_alive' => $this->piglets_born_alive,
            'piglets_stillborn' => $this->piglets_stillborn,
            'piglets_weaned' => $this->piglets_weaned,
            'notes' => $this->notes,
            'flock_name' => $this->whenLoaded('flock', fn () => $this->flock->name),
            'boar_flock_name' => $this->whenLoaded('boarFlock', fn () => $this->boarFlock?->name),
        ];
    }
}
