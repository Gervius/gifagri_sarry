<?php

namespace App\Actions;

use App\Models\AnimalType;
use App\Models\Building;
use App\Models\Flock;
use App\Services\SequenceGeneratorService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StoreFlockAction
{
    public function __construct(
        private SequenceGeneratorService $sequenceGenerator
    ) {}

    public function execute(Collection $data, int $userId): Flock
    {
        $building = Building::with('site')->findOrFail($data['building_id']);
        $animalType = AnimalType::findOrFail($data['animal_type_id']);

        $name = $data->get('name') ?: $this->sequenceGenerator->generateFlockName(
            $animalType,
            $building,
            Carbon::parse($data['arrival_date'])
        );

        return Flock::create($data->merge([
            'name' => $name,
            'created_by' => $userId,
        ])->toArray());
    }
}
