<?php

namespace App\Actions;

use App\Models\PigBreedingEvent;
use App\Models\Flock;
use Illuminate\Support\Facades\DB;

class ProcessPigWeaningAction
{
    public function execute(PigBreedingEvent $weaningEvent, int $userId): ?Flock
    {
        $newFlock = null;

        DB::transaction(function () use ($weaningEvent, $userId, &$newFlock) {
            $sowFlock = $weaningEvent->flock;

            // Validation : vérifier que le nombre de porcelets sevrés est cohérent
            if ($weaningEvent->piglets_weaned === null || $weaningEvent->piglets_weaned <= 0) {
                throw new \InvalidArgumentException('Le nombre de porcelets sevrés doit être supérieur à zéro.');
            }

            // Clôturer la lactation : mettre à jour les notes de la mère
            $sowFlock->update([
                'notes' => ($sowFlock->notes ?? '') . "\nSevrage effectué le " . now()->format('d/m/Y') . " - {$weaningEvent->piglets_weaned} porcelets sevrés"
            ]);

            // Créer un nouveau lot d'engraissement (fattening)
            $newFlock = Flock::create([
                'name' => $sowFlock->name . ' - Porcelets sevrés ' . now()->format('d/m'),
                'species' => 'pig',
                'animal_type_id' => $sowFlock->animal_type_id, // Même type d'animal
                'building_id' => $sowFlock->building_id, // Même bâtiment (peut être différent en pratique)
                'arrival_date' => now(),
                'initial_quantity' => $weaningEvent->piglets_weaned,
                'current_quantity' => $weaningEvent->piglets_weaned,
                'status' => 'draft',
                'created_by' => $userId,
                'notes' => "Lot d'engraissement créé à partir du sevrage de {$sowFlock->name}",
            ]);

            // Enregistrer le lien parent-enfant si utile (via notes ou un futur champ)
            $newFlock->update([
                'notes' => $newFlock->notes . "\nMère: {$sowFlock->name} (ID: {$sowFlock->id})"
            ]);
        });

        return $newFlock;
    }
}
