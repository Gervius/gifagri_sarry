<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\FlockCreated;
use App\Models\Flock;

class FlockObserver
{
    /**
     * Déclencher l'événement FlockCreated quand un Flock est créé.
     */
    public function created(Flock $flock): void
    {
        FlockCreated::dispatch($flock);
    }

    /**
     * Handle the Flock "updated" event.
     */
    public function updated(Flock $flock): void
    {
        //
    }

    /**
     * Handle the Flock "deleted" event.
     */
    public function deleted(Flock $flock): void
    {
        //
    }

    /**
     * Handle the Flock "restored" event.
     */
    public function restored(Flock $flock): void
    {
        //
    }

    /**
     * Handle the Flock "force deleted" event.
     */
    public function forceDeleted(Flock $flock): void
    {
        //
    }
}
