<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DailyRecordApproved;
use App\Models\EggMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessEggProduction
{
    /**
     * Listener synchrone qui crée une entrée dans egg_movements
     * si des œufs ont été produits.
     */
    public function handle(DailyRecordApproved $event): void
    {
        try {
            $dailyRecord = $event->dailyRecord;

            // Vérifier si eggs > 0
            if ($dailyRecord->eggs <= 0) {
                return;
            }

            // Encapsuler dans une transaction
            DB::transaction(function () use ($dailyRecord): void {
                // Créer une entrée dans egg_movements
                EggMovement::create([
                    'date' => $dailyRecord->date,
                    'type' => 'in',
                    'quantity' => $dailyRecord->eggs,
                    'source_type' => $dailyRecord::class,
                    'source_id' => $dailyRecord->id,
                    'created_by' => Auth::id() ?? $dailyRecord->approved_by,
                ]);

                Log::info('Egg production recorded', [
                    'daily_record_id' => $dailyRecord->id,
                    'quantity' => $dailyRecord->eggs,
                    'date' => $dailyRecord->date,
                ]);
            });
        } catch (\Exception $exception) {
            Log::error('Error processing egg production', [
                'daily_record_id' => $event->dailyRecord->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
