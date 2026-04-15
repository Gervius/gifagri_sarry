<?php

namespace App\Strategies\Species;

use App\Models\Flock;

class BroilerBehavior implements SpeciesBehaviorStrategy
{
    public function canCollectEggs(Flock $flock): bool
    {
        // Broilers ne pondent pas
        return false;
    }

    public function calculateGMQ(Flock $flock): ?float
    {
        // Pas de GMQ pour broilers
        return null;
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // Les broilers acceptent les fiches journalières
        return true;
    }
}