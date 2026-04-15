<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SettingsUpdatedEvent;
use App\Services\ReferenceParameterCacheService;

class InvalidateReferenceCache
{
    public function __construct(
        private ReferenceParameterCacheService $cacheService,
    ) {}

    public function handle(SettingsUpdatedEvent $event): void
    {
        $this->cacheService->refresh($event->entity);
    }
}
