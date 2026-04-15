<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TreatmentApproved;
use App\Models\Ingredient;
use App\Models\ScheduledTreatment;
use App\Services\StockManagerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeductMedicationFromStock
{
    public function __construct(
        private StockManagerService $stockManagerService,
    ) {}

    /**
     * Listener synchrone qui déduit le médicament/vaccin du lot correspondant.
     * Crée un mouvement de stock et met à jour les scheduled_treatments associés.
     */
    public function handle(TreatmentApproved $event): void
    {
        try {
            $treatment = $event->treatment;

            // Vérifier si un batch_id est renseigné
            if (! $treatment->batch_id) {
                Log::info('No batch associated with treatment', [
                    'treatment_id' => $treatment->id,
                ]);

                return;
            }

            // Encapsuler dans une transaction pour garantir l'intégrité
            DB::transaction(function () use ($treatment): void {
                // Récupérer le batch
                $batch = $treatment->batch()->first();

                if (! $batch) {
                    Log::warning('Batch not found for treatment', [
                        'treatment_id' => $treatment->id,
                        'batch_id' => $treatment->batch_id,
                    ]);

                    return;
                }

                // Vérifier que le batchable est un Ingredient
                if (! $batch->batchable instanceof Ingredient) {
                    Log::warning('Batch is not associated with an Ingredient', [
                        'treatment_id' => $treatment->id,
                        'batch_id' => $batch->id,
                        'batchable_type' => $batch->batchable_type,
                    ]);

                    return;
                }

                $quantityConsumed = 1; // Une unité de dose consommée

                // Utiliser le StockManagerService pour décrémenter le stock
                $this->stockManagerService->decreaseStock(
                    $batch,
                    $quantityConsumed,
                    'medication_consumption',
                    "Traitement approuvé ID #{$treatment->id}"
                );

                // Mettre à jour les scheduled_treatments associés
                ScheduledTreatment::query()
                    ->where('flock_id', $treatment->flock_id)
                    ->whereNull('actual_treatment_id')
                    ->where('status', '!=', 'cancelled')
                    ->update([
                        'actual_treatment_id' => $treatment->id,
                        'status' => 'completed',
                        'updated_at' => now(),
                    ]);

                Log::info('Medication deducted from stock and treatment scheduled', [
                    'treatment_id' => $treatment->id,
                    'batch_id' => $batch->id,
                    'quantity_consumed' => $quantityConsumed,
                    'remaining_stock' => $batch->fresh()->current_quantity,
                ]);
            });
        } catch (\Exception $exception) {
            Log::error('Error deducting medication from stock', [
                'treatment_id' => $event->treatment->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
