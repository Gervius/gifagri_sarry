<?php

namespace App\Strategies\Species;

use App\Models\Flock;

class PigBehavior implements SpeciesBehaviorStrategy
{
    public function canCollectEggs(Flock $flock): bool
    {
        // Porcs ne pondent pas
        return false;
    }

    public function calculateGMQ(Flock $flock): ?float
    {
        // Pas de GMQ pour porcs
        return null;
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // Les porcs acceptent les fiches journalières (délégué aux sous-stratégies)
        return true;
    }
}