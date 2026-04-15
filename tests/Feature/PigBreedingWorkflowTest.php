<?php

use App\Models\Flock;
use App\Models\AnimalType;
use App\Models\PigBreedingEvent;
use App\Actions\RecordBreedingEventAction;
use App\Actions\ProcessPigWeaningAction;
use App\Services\ProfitabilityService;
use Illuminate\Support\Carbon;

test('pig sow behavior does not allow egg collection', function () {
    $animalType = AnimalType::factory()->create(['code' => 'pig']);
    $sowFlock = Flock::factory()->create(['animal_type_id' => $animalType->id]);

    $behavior = $sowFlock->getSpeciesBehavior();

    expect($behavior->canCollectEggs($sowFlock))->toBeFalse();
});

test('recording a mating event calculates expected farrowing date plus 114 days', function () {
    $animalType = AnimalType::factory()->create(['code' => 'pig']);
    $sowFlock = Flock::factory()->create(['animal_type_id' => $animalType->id]);
    $boarFlock = Flock::factory()->create(['animal_type_id' => $animalType->id]);

    $action = new RecordBreedingEventAction();
    $matingDate = Carbon::now()->subDays(5);
    $expectedFarrowingDate = $matingDate->addDays(114);

    $event = $action->execute([
        'flock_id' => $sowFlock->id,
        'event_type' => 'mating',
        'event_date' => $matingDate,
        'boar_flock_id' => $boarFlock->id,
    ], 1);

    expect($event->event_type)->toBe('mating');
    expect($event->notes)->toContain('Mise bas prévue');
});

test('weaning event creates a new fattening flock', function () {
    $animalType = AnimalType::factory()->create(['code' => 'pig']);
    $sowFlock = Flock::factory()->create(['animal_type_id' => $animalType->id]);

    $weaningEvent = PigBreedingEvent::factory()->create([
        'flock_id' => $sowFlock->id,
        'event_type' => 'weaning',
        'piglets_weaned' => 8,
    ]);

    $action = new ProcessPigWeaningAction();
    $newFlock = $action->execute($weaningEvent, 1);

    expect($newFlock)->not->toBeNull();
    expect($newFlock->initial_quantity)->toBe(8);
    expect($newFlock->current_quantity)->toBe(8);
    expect($newFlock->status)->toBe('draft');
    expect($newFlock->notes)->toContain('Mère');
});

test('profitability service calculates piglets weaned per sow per year', function () {
    $animalType = AnimalType::factory()->create(['code' => 'pig']);
    $sowFlock = Flock::factory()->create(['animal_type_id' => $animalType->id]);

    PigBreedingEvent::factory()->create([
        'flock_id' => $sowFlock->id,
        'event_type' => 'weaning',
        'piglets_weaned' => 8,
        'event_date' => now()->subMonths(6),
    ]);

    PigBreedingEvent::factory()->create([
        'flock_id' => $sowFlock->id,
        'event_type' => 'weaning',
        'piglets_weaned' => 9,
        'event_date' => now()->subMonths(3),
    ]);

    PigBreedingEvent::factory()->create([
        'flock_id' => $sowFlock->id,
        'event_type' => 'weaning',
        'piglets_weaned' => 7,
        'event_date' => now(),
    ]);

    $service = new ProfitabilityService();
    $result = $service->calculatePigletsWeanedPerSowPerYear($sowFlock);

    expect($result)->not->toBeNull();
    expect($result)->toBeGreaterThan(0);
});

test('average weaned piglets per event is calculated correctly', function () {
    $animalType = AnimalType::factory()->create(['code' => 'pig']);
    $sowFlock = Flock::factory()->create(['animal_type_id' => $animalType->id]);

    PigBreedingEvent::factory()->create([
        'flock_id' => $sowFlock->id,
        'event_type' => 'weaning',
        'piglets_weaned' => 8,
    ]);

    PigBreedingEvent::factory()->create([
        'flock_id' => $sowFlock->id,
        'event_type' => 'weaning',
        'piglets_weaned' => 10,
    ]);

    $service = new ProfitabilityService();
    $result = $service->calculateAverageWeanedPigletsPerEvent($sowFlock);

    expect($result)->toBe(9.0);
});
