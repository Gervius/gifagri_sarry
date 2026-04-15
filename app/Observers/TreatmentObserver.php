<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\TreatmentApproved;
use App\Models\Treatment;

class TreatmentObserver
{
    /**
     * Observer la méthode updated() du modèle Treatment.
     * Si le status change vers 'approved', dispatcher l'événement.
     */
    public function updated(Treatment $treatment): void
    {
        // Vérifier si la colonne 'status' a changé et si la nouvelle valeur est 'approved'
        if ($treatment->isDirty('status') && $treatment->status === 'approved') {
            TreatmentApproved::dispatch($treatment);
        }
    }

    /**
     * Handle the Treatment "created" event.
     */
    public function created(Treatment $treatment): void
    {
        //
    }

    /**
     * Handle the Treatment "deleted" event.
     */
    public function deleted(Treatment $treatment): void
    {
        //
    }

    /**
     * Handle the Treatment "restored" event.
     */
    public function restored(Treatment $treatment): void
    {
        //
    }

    /**
     * Handle the Treatment "force deleted" event.
     */
    public function forceDeleted(Treatment $treatment): void
    {
        //
    }
}
