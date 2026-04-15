<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AnimalType;
use App\Models\Building;
use App\Models\Flock;
use Carbon\Carbon;

class SequenceGeneratorService
{
    public function generateFlockName(AnimalType $type, Building $building, Carbon $date): string
    {
        $prefix = 'G';
        $speciesCode = $type->code;
        $siteCode = $building->site->short_name;
        $dateFormatted = $date->format('d-m-y');

        $baseName = "{$prefix}-{$speciesCode}-{$siteCode}-{$dateFormatted}";

        // Vérifier les doublons et ajouter suffixe si nécessaire
        $counter = 0;
        $name = $baseName;

        while (Flock::where('name', $name)->exists()) {
            $counter++;
            $suffix = str_pad((string) $counter, 2, '0', STR_PAD_LEFT);
            $name = "{$baseName}-{$suffix}";
        }

        return $name;
    }
}
