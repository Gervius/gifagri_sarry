<?php

namespace App\Strategies\Species;

use App\Models\Flock;

class LayerBehavior implements SpeciesBehaviorStrategy
{
    public function canCollectEggs(Flock $flock): bool
    {
        // Logique basée sur la phase de production
        // Par exemple, si phase actuelle autorise la ponte
        $currentPhase = $flock->getCurrentPhase();
        return $currentPhase && $currentPhase->allowsEggCollection();
    }

    public function calculateGMQ(Flock $flock): ?float
    {
        // Calcul du GMQ pour layers
        // GMQ = (œufs collectés / nombre d'animaux) * 100 ou quelque chose
        // Implémentation simplifiée
        return null; // À implémenter selon les règles métier
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // Les layers acceptent les fiches journalières
        return true;
    }
}