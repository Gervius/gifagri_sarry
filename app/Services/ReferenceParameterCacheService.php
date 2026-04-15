<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReferenceParameterCacheService
{
    private const ENTITY_KEYS = [
        'animal_types' => 'reference_parameters:animal_types',
        'production_phases' => 'reference_parameters:production_phases',
        'accounting_rules' => 'reference_parameters:accounting_rules',
        'units' => 'reference_parameters:units',
    ];

    public function getAnimalTypes(): Collection
    {
        return $this->rememberForever('animal_types');
    }

    public function getProductionPhases(): Collection
    {
        return $this->rememberForever('production_phases');
    }

    public function getAccountingRules(): Collection
    {
        return $this->rememberForever('accounting_rules');
    }

    public function getUnits(): Collection
    {
        return $this->rememberForever('units');
    }

    public function refresh(string $entity): Collection
    {
        $key = $this->cacheKey($entity);
        $value = $this->loadEntity($entity);

        Cache::forever($key, $value);

        return $value;
    }

    public function invalidate(string $entity): void
    {
        Cache::forget($this->cacheKey($entity));
    }

    private function rememberForever(string $entity): Collection
    {
        $key = $this->cacheKey($entity);

        return Cache::rememberForever($key, fn () => $this->loadEntity($entity));
    }

    private function cacheKey(string $entity): string
    {
        if (! array_key_exists($entity, self::ENTITY_KEYS)) {
            throw new \InvalidArgumentException("Unsupported reference entity: {$entity}");
        }

        return self::ENTITY_KEYS[$entity];
    }

    private function loadEntity(string $entity): Collection
    {
        return match ($entity) {
            'animal_types' => DB::table('animal_types')->where('is_active', true)->orderBy('name')->get(),
            'production_phases' => DB::table('production_phases')->orderBy('animal_type_id')->orderBy('order')->get(),
            'accounting_rules' => DB::table('accounting_rules')->where('is_active', true)->orderBy('name')->get(),
            'units' => DB::table('units')->orderBy('name')->get(),
            default => throw new \InvalidArgumentException("Unsupported reference entity: {$entity}"),
        };
    }
}
