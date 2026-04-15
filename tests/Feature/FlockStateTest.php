<?php

use App\Models\Flock;
use App\Models\AnimalType;
use App\States\Flock\FlockStateFactory;

test('flock draft state cannot submit daily record', function () {
    $animalType = AnimalType::factory()->create(['code' => 'layer']);
    $flock = Flock::factory()->create(['status' => 'draft', 'animal_type_id' => $animalType->id]);

    $state = FlockStateFactory::create($flock->status);

    expect($state->canSubmitDailyRecord($flock))->toBeFalse();
});

test('broiler behavior does not allow egg collection', function () {
    $animalType = \App\Models\AnimalType::factory()->create(['code' => 'broiler']);
    $flock = \App\Models\Flock::factory()->create(['animal_type_id' => $animalType->id]);

    $behavior = $flock->getSpeciesBehavior();

    expect($behavior->canCollectEggs($flock))->toBeFalse();
});
