<?php

declare(strict_types=1);

namespace App\Events;

class SettingsUpdatedEvent
{
    public function __construct(
        public string $entity,
    ) {}
}
