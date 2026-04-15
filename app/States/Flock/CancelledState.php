<?php

namespace App\States\Flock;

use App\Models\Flock;

class CancelledState implements FlockState
{
    public function canTransitionToActive(Flock $flock): bool
    {
        return false;
    }

    public function transitionToActive(Flock $flock): void
    {
        // Rien
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        return false; // Annulé
    }

    public function getStatus(): string
    {
        return 'cancelled';
    }
}