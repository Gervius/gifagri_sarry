<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DailyRecordApproved;
use App\Exceptions\InsufficientFeedStockException;
use App\Models\Batch;
use App\Services\StockManagerService;
use Illuminate\Support\Facades\Log;

class DeductFeedFromStock
{
    public function __construct(
        private StockManagerService $stockManagerService,
    ) {}

    /**
     * Listener synchrone qui déduit l'aliment du lot correspondant.
     * Si le stock est insuffisant, lève une exception métier.
     */
    public function handle(DailyRecordApproved $event): void
    {
        try {
            $dailyRecord = $event->dailyRecord;

            // Vérifier si feed_consumed > 0 et feed_batch_id est renseigné
            if ($dailyRecord->feed_consumed <= 0 || ! $dailyRecord->feed_batch_id) {
                return;
            }

            // Récupérer le lot d'aliment
            $batch = Batch::findOrFail($dailyRecord->feed_batch_id);

            // Utiliser le StockManagerService pour décrémenter le stock
            $this->stockManagerService->decreaseStock(
                $batch,
                $dailyRecord->feed_consumed,
                'feed_consumption',
                "Consommation d'aliment pour l'enregistrement quotidien {$dailyRecord->id}"
            );

            Log::info('Feed deducted from stock', [
                'batch_id' => $batch->id,
                'daily_record_id' => $dailyRecord->id,
                'quantity' => $dailyRecord->feed_consumed,
            ]);
        } catch (InsufficientFeedStockException $exception) {
            Log::error('Insufficient feed stock', [
                'batch_id' => $exception->batchId,
                'required' => $exception->required,
                'available' => $exception->available,
            ]);

            throw $exception;
        } catch (\Exception $exception) {
            Log::error('Error deducting feed from stock', [
                'daily_record_id' => $event->dailyRecord->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
