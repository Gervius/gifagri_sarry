<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'animal_type_id' => $this->animal_type_id,
            'animal_type_code' => $this->animalType->code,
            'animal_type_name' => $this->animalType->name,
            'building_id' => $this->building_id,
            'building' => $this->building->name,
            'arrival_date' => $this->arrival_date->format('Y-m-d'),
            'arrival_date_formatted' => $this->arrival_date->format('d/m/Y'),
            'initial_quantity' => $this->initial_quantity,
            'current_quantity' => $this->current_quantity,
            'status' => $this->status,
            'is_legacy' => $this->is_legacy,
            'standard_mortality_rate' => $this->standard_mortality_rate,
            'notes' => $this->notes,
            'creator' => $this->creator->name,
            'approver' => $this->approver?->name,
            'approved_at' => $this->approved_at?->format('d/m/Y H:i'),
            'ended_at' => $this->ended_at?->format('d/m/Y'),
            'end_reason' => $this->end_reason,
            'created_at' => $this->created_at->format('d/m/Y H:i'),

            // Feature flags pour le frontend
            'features' => [
                'has_eggs' => (bool) $this->animalType->can_lay_eggs,
                'has_gmq' => (bool) $this->animalType->has_growth_tracking,
                'is_breeding' => (bool) $this->animalType->has_breeding_cycle,
            ],

            // Statistiques calculées (uniquement pour les lots actifs)
            'stats' => $this->when($this->status === 'active', function () {
                $stats = [
                    'mortality_rate' => $this->calculateMortalityRate(),
                    'total_losses' => $this->dailyRecords()->where('status', 'approved')->sum('losses'),
                ];

                if ($this->animalType->can_lay_eggs) {
                    $totalEggs = $this->dailyRecords()->where('status', 'approved')->sum('eggs');
                    $daysWithRecords = $this->dailyRecords()->where('status', 'approved')->count();
                    $stats['total_eggs'] = $totalEggs;
                    $stats['avg_eggs_per_day'] = $daysWithRecords > 0 ? round($totalEggs / $daysWithRecords) : 0;
                    $stats['egg_efficiency'] = $this->calculateEggEfficiency();
                }

                if ($this->animalType->has_growth_tracking) {
                    $behavior = $this->getSpeciesBehavior();
                    $stats['gmq'] = $behavior->calculateGMQ($this);
                    $stats['ic'] = $this->calculateIC(); // À implémenter
                }

                if ($this->animalType->has_breeding_cycle) {
                    // Pour les truies reproductrices
                    $lastFarrowing = $this->pigBreedingEvents()
                        ->where('event_type', 'farrowing')
                        ->latest('event_date')
                        ->first();

                    $stats['born_alive'] = $lastFarrowing?->piglets_born_alive ?? 0;
                    $stats['stillborn'] = $lastFarrowing?->piglets_stillborn ?? 0;
                    $stats['weaned'] = $lastFarrowing?->piglets_weaned ?? 0;
                    $stats['weaning_rate'] = $lastFarrowing && $lastFarrowing->piglets_born_alive > 0
                        ? round(($lastFarrowing->piglets_weaned / $lastFarrowing->piglets_born_alive) * 100)
                        : 0;
                    $stats['litters_per_year'] = $this->calculateLittersPerYear();
                }

                return $stats;
            }),

            // Permissions calculées
            'can' => [
                'view' => $user->can('view', $this->resource),
                'update' => $user->can('update', $this->resource),
                'delete' => $user->can('delete', $this->resource),
                'submit' => $user->can('submit', $this->resource),
                'approve' => $user->can('approve', $this->resource),
                'reject' => $user->can('reject', $this->resource),
                'end' => $user->can('end', $this->resource),
            ],

            // Relations chargées conditionnellement
            'daily_records' => DailyRecordResource::collection($this->whenLoaded('dailyRecords')),
            'weight_records' => WeightRecordResource::collection($this->whenLoaded('weightRecords')),
            'breeding_events' => PigBreedingEventResource::collection($this->whenLoaded('pigBreedingEvents')),
        ];
    }

    // Méthodes helper (exemples)
    private function calculateMortalityRate(): float
    {
        if ($this->initial_quantity == 0) return 0;
        $totalLosses = $this->dailyRecords()->where('status', 'approved')->sum('losses');
        return round(($totalLosses / $this->initial_quantity) * 100, 2);
    }

    private function calculateEggEfficiency(): float
    {
        if ($this->current_quantity == 0) return 0;
        $totalEggs = $this->dailyRecords()->where('status', 'approved')->sum('eggs');
        $daysActive = max(1, $this->arrival_date->diffInDays(now()));
        return round(($totalEggs / ($this->current_quantity * $daysActive)) * 100, 2);
    }

    private function calculateIC(): ?float
    {
        // Indice de consommation = total aliment / (poids total vendu + biomasse actuelle)
        // À implémenter selon vos données
        return null;
    }

    private function calculateLittersPerYear(): float
    {
        // Nombre de portées par an pour cette truie
        $firstEvent = $this->pigBreedingEvents()->oldest('event_date')->first();
        if (!$firstEvent) return 0;
        $daysSinceFirst = max(1, $firstEvent->event_date->diffInDays(now()));
        $farrowings = $this->pigBreedingEvents()->where('event_type', 'farrowing')->count();
        return round(($farrowings / $daysSinceFirst) * 365, 2);
    }
}