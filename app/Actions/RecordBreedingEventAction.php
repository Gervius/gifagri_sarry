<?php

namespace App\Actions;

use App\Models\PigBreedingEvent;
use App\Models\Flock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RecordBreedingEventAction
{
    public function execute(array $data, int $userId): PigBreedingEvent
    {
        $pigBreedingEvent = null;

        DB::transaction(function () use ($data, &$pigBreedingEvent) {
            $pigBreedingEvent = PigBreedingEvent::create($data);

            if ($data['event_type'] === 'mating') {
                $this->handleMatingEvent($pigBreedingEvent);
            } elseif ($data['event_type'] === 'farrowing') {
                $this->handleFarrowingEvent($pigBreedingEvent);
            }
        });

        return $pigBreedingEvent;
    }

    private function handleMatingEvent(PigBreedingEvent $event): void
    {
        // Calcul de la date de mise bas : date de saillie + 114 jours
        $expectedFarrowingDate = Carbon::parse($event->event_date)->addDays(114);

        // Créer une alerte/événement programmé via une note ou un attribut
        // Pour l'instant, stocker l'info dans les notes
        $event->update([
            'notes' => ($event->notes ?? '') . "\n[Mise bas prévue: " . $expectedFarrowingDate->format('d/m/Y') . "]"
        ]);
    }

    private function handleFarrowingEvent(PigBreedingEvent $event): void
    {
        // Update du flock avec les données de mise bas
        $event->flock->update([
            'notes' => ($event->flock->notes ?? '') . "\nMise bas enregistrée le " . now()->format('d/m/Y')
        ]);
    }
}
