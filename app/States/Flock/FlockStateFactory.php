<?php

namespace App\States\Flock;

use App\Models\Flock;
use InvalidArgumentException;

class FlockStateFactory
{
    public static function create(string $status): FlockState
    {
        return match ($status) {
            'draft' => new DraftState(),
            'active' => new ActiveState(),
            'completed' => new CompletedState(),
            'cancelled' => new CancelledState(),
            default => throw new InvalidArgumentException("Unknown status: {$status}"),
        };
    }
}