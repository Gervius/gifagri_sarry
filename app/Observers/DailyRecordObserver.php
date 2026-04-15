<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\DailyRecordApproved;
use App\Models\DailyRecord;

class DailyRecordObserver
{
    /**
     * Observer la méthode updated() du modèle DailyRecord.
     * Si le status change vers 'approved', dispatcher l'événement.
     */
    public function updated(DailyRecord $dailyRecord): void
    {
        // Vérifier si la colonne 'status' a changé et si la nouvelle valeur est 'approved'
        if ($dailyRecord->isDirty('status') && $dailyRecord->status === 'approved') {
            DailyRecordApproved::dispatch($dailyRecord);
        }
    }

    /**
     * Handle the DailyRecord "created" event.
     */
    public function created(DailyRecord $dailyRecord): void
    {
        //
    }

    /**
     * Handle the DailyRecord "deleted" event.
     */
    public function deleted(DailyRecord $dailyRecord): void
    {
        //
    }

    /**
     * Handle the DailyRecord "restored" event.
     */
    public function restored(DailyRecord $dailyRecord): void
    {
        //
    }

    /**
     * Handle the DailyRecord "force deleted" event.
     */
    public function forceDeleted(DailyRecord $dailyRecord): void
    {
        //
    }
}
