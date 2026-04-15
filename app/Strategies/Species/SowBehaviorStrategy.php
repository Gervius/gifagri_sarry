<?php

namespace App\Strategies\Species;

use App\Models\Flock;

class SowBehaviorStrategy implements SpeciesBehaviorStrategy
{
    public function canCollectEggs(Flock $flock): bool
    {
        // Les truies ne pondent pas
        return false;
    }

    public function calculateGMQ(Flock $flock): ?float
    {
        // Le GMQ n'est pas pertinent pour les truies (reproduction/maternité)
        return null;
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // Les truies acceptent les fiches journalières (consommation, santé, etc.)
        // mais pas de collecte d'œufs
        return true;
    }
}
