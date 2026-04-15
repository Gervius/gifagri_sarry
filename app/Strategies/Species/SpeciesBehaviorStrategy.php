<?php

namespace App\Strategies\Species;

use App\Models\Flock;

interface SpeciesBehaviorStrategy
{
    public function canCollectEggs(Flock $flock): bool;
    public function calculateGMQ(Flock $flock): ?float;
    public function canSubmitDailyRecord(Flock $flock): bool;
}