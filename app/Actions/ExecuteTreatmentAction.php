<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\TreatmentExecutedEvent;
use App\Models\ScheduledTreatment;
use App\Models\Treatment;
use App\Services\StockManagerService;
use Illuminate\Support\Facades\DB;

class ExecuteTreatmentAction
{
    public function __construct(
        private StockManagerService $stockManager,
    ) {}

    public function execute(Treatment $treatment, int $executorId, ?ScheduledTreatment $scheduledTreatment = null): Treatment
    {
        return DB::transaction(function () use ($treatment, $executorId, $scheduledTreatment): Treatment {
            if ($treatment->status === 'executed') {
                throw new \InvalidArgumentException('Ce traitement a déjà été exécuté.');
            }

            if ($treatment->batch_id !== null) {
                $this->stockManager->decreaseStock(
                    $treatment->batch,
                    1.0,
                    'treatment_execution',
                    "Consommation du lot pour le traitement #{$treatment->id}"
                );
            }

            $scheduledTreatment = $scheduledTreatment ?? $this->findLinkedScheduledTreatment($treatment);

            if ($scheduledTreatment !== null) {
                $scheduledTreatment->update([
                    'status' => 'completed',
                    'actual_treatment_id' => $treatment->id,
                ]);
            }

            $treatment->update([
                'status' => 'executed',
            ]);

            TreatmentExecutedEvent::dispatch($treatment, $scheduledTreatment);

            return $treatment;
        });
    }

    private function findLinkedScheduledTreatment(Treatment $treatment): ?ScheduledTreatment
    {
        return ScheduledTreatment::query()
            ->where('flock_id', $treatment->flock_id)
            ->where('scheduled_date', $treatment->treatment_date)
            ->whereNull('actual_treatment_id')
            ->where('status', '!=', 'cancelled')
            ->first();
    }
}
