<?php

namespace App\Actions;

use App\Models\Flock;
use Illuminate\Support\Facades\DB;

class ApproveFlockAction
{
    public function execute(Flock $flock, int $approverId): void
    {
        DB::transaction(function () use ($flock, $approverId) {
            if (!$flock->canTransitionToActive()) {
                throw new \InvalidArgumentException('Cannot approve flock: conditions not met.');
            }

            $flock->transitionToActive();
            $flock->approved_by = $approverId;
            $flock->approved_at = now();
            $flock->save();
        });
    }
}