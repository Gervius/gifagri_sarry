<?php

namespace App\States\Flock;

use App\Models\Flock;

class ActiveState implements FlockState
{
    public function canTransitionToActive(Flock $flock): bool
    {
        return false; // Déjà actif
    }

    public function transitionToActive(Flock $flock): void
    {
        // Rien à faire
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // Utiliser la stratégie de l'espèce
        return $flock->getSpeciesBehavior()->canCollectEggs($flock);
    }

    public function getStatus(): string
    {
        return 'active';
    }
}