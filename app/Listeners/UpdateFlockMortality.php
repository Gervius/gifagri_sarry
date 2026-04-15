<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DailyRecordApproved;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateFlockMortality
{
    /**
     * Listener synchrone qui décrémente la current_quantity du Flock
     * en cas de mortalité.
     */
    public function handle(DailyRecordApproved $event): void
    {
        try {
            $dailyRecord = $event->dailyRecord;

            // Vérifier si losses > 0
            if ($dailyRecord->losses <= 0) {
                return;
            }

            // Encapsuler dans une transaction
            DB::transaction(function () use ($dailyRecord): void {
                $flock = $dailyRecord->flock()->lockForUpdate()->firstOrFail();

                // Décrémenter la current_quantity du Flock
                $flock->decrement('current_quantity', $dailyRecord->losses);

                Log::info('Flock mortality updated', [
                    'flock_id' => $flock->id,
                    'daily_record_id' => $dailyRecord->id,
                    'losses' => $dailyRecord->losses,
                    'new_quantity' => $flock->fresh()->current_quantity,
                ]);
            });
        } catch (\Exception $exception) {
            Log::error('Error updating flock mortality', [
                'flock_id' => $event->dailyRecord->flock_id,
                'daily_record_id' => $event->dailyRecord->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
