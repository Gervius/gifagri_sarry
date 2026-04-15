<?php

namespace App\States\Flock;

use App\Models\Flock;
use InvalidArgumentException;

class DraftState implements FlockState
{
    public function canTransitionToActive(Flock $flock): bool
    {
        // Pour les legacy, pas besoin de facture
        if ($flock->is_legacy) {
            return true;
        }
        // Sinon, besoin de invoice_id
        return !is_null($flock->invoice_id);
    }

    public function transitionToActive(Flock $flock): void
    {
        if (!$this->canTransitionToActive($flock)) {
            throw new InvalidArgumentException('Cannot transition to active: missing invoice for non-legacy flock.');
        }
        $flock->status = 'active';
        $flock->save();
    }

    public function canSubmitDailyRecord(Flock $flock): bool
    {
        // En draft, pas de saisie
        return false;
    }

    public function getStatus(): string
    {
        return 'draft';
    }
}