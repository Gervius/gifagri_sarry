<?php

namespace App\Strategies\Species;

use App\Models\Flock;
use InvalidArgumentException;

class SpeciesBehaviorFactory
{
    public static function create(Flock $flock): SpeciesBehaviorStrategy
    {
        return match ($flock->animalType->code) {
            'layer' => new LayerBehavior(),
            'broiler' => new BroilerBehavior(),
            'pig' => self::resolvePigBehavior($flock),
            default => throw new InvalidArgumentException("Unknown species code: {$flock->animalType->code}"),
        };
    }

    private static function resolvePigBehavior(Flock $flock): SpeciesBehaviorStrategy
    {
        // Résoudre dynamiquement selon la phase du lot
        $currentPhase = $flock->getCurrentPhase();

        if ($currentPhase && in_array($currentPhase->name, ['Reproduction', 'Maternité', 'Lactation'], true)) {
            return new SowBehaviorStrategy();
        }

        // Par défaut ou si phase est "Engraissement", etc.
        return new FatteningPigBehaviorStrategy();
    }
}