<?php

namespace App\Strategies\Species;

use App\Models\Flock;

class FatteningPigBehaviorStrategy implements SpeciesBehaviorStrategy
{
    public function canCollectEggs(Flock $flock): bool
    {
        // Les porcs engraissés ne pondent pas
        return false;
    }

    public function calculateGMQ(Flock $flock): ?float
    {
        // Calcul du GMQ pour les porcs en engraissement
        // GMQ = (Poids actuel - Poids initial) / Nombre de jours d'élevage / Nombre d'animaux
        $lastWeightRecord = $flock->weightRecords()->latest()->first();
        if (!$lastWeightRecord) {
            return null;
        }

        $currentDate = now();
        $arrivalDate = $flock->arrival_date;
        $daysElapsed = $currentDate->diffInDays($arrivalDate);

        if ($daysElapsed <= 0 || $flock->current_quantity <= 0) {
            return null;
        }

        // Estimer le poids initial (première pesée ou assomption)
        $firstWeightRecord = $flock->weightRecords()->oldest()->first();
        $initialWeight = $firstWeightRecord?->average_weight ?? 25; // 25 kg par défaut pour porcelets

        $weightGain = ($lastWeightRecord->average_weight - $initialWeight) * $flock->current_quantity;
        $gmq = $weightGain / $daysElapsed / $flock->current_quantity;

        return round($gmq, 2);
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // Les porcs en engraissement acceptent fiches journalières
        return true;
    }
}
