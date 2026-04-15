<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\SettingsUpdatedEvent;
use Illuminate\Database\Eloquent\Model;

class ReferenceParameterObserver
{
    public function saved(Model $model): void
    {
        $this->dispatchEvent($model);
    }

    public function deleted(Model $model): void
    {
        $this->dispatchEvent($model);
    }

    private function dispatchEvent(Model $model): void
    {
        $entity = match (true) {
            $model instanceof \App\Models\AnimalType => 'animal_types',
            $model instanceof \App\Models\ProductionPhase => 'production_phases',
            $model instanceof \App\Models\AccountingRule => 'accounting_rules',
            $model instanceof \App\Models\Unit => 'units',
            default => null,
        };

        if ($entity) {
            SettingsUpdatedEvent::dispatch($entity);
        }
    }
}
