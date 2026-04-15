<?php

namespace App\States\Flock;

use App\Models\Flock;

interface FlockState
{
    public function canTransitionToActive(Flock $flock): bool;
    public function transitionToActive(Flock $flock): void;
    public function canSubmitDailyRecord(Flock $flock): bool;
    public function getStatus(): string;
}