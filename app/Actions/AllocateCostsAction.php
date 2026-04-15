<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\AnalyticalAllocation;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class AllocateCostsAction
{
    public function execute(JournalEntry $journalEntry, array $allocations): void
    {
        DB::transaction(function () use ($journalEntry, $allocations): void {
            $this->validateAllocations($journalEntry, $allocations);

            $baseAmount = max($journalEntry->debit, $journalEntry->credit);

            foreach ($allocations as $allocation) {
                $percentage = (float) ($allocation['percentage'] ?? 0);
                $this->validateAllocationItem($allocation);

                $amount = isset($allocation['amount'])
                    ? (float) $allocation['amount']
                    : round($baseAmount * $percentage / 100, 2);

                AnalyticalAllocation::create([
                    'journal_entry_id' => $journalEntry->id,
                    'analytical_account_id' => $allocation['analytical_account_id'],
                    'percentage' => $percentage,
                    'amount' => $amount,
                ]);
            }
        });
    }

    private function validateAllocations(JournalEntry $journalEntry, array $allocations): void
    {
        if (empty($allocations)) {
            throw new \InvalidArgumentException('Aucune ventilation analytique fournie.');
        }

        if ($journalEntry->analyticalAllocations()->exists()) {
            throw new \InvalidArgumentException('Cette écriture comptable a déjà des allocations analytiques.');
        }

        $totalPercentage = array_reduce($allocations, fn($carry, $item) => $carry + (float) ($item['percentage'] ?? 0), 0.0);

        if (round($totalPercentage, 2) !== 100.00) {
            throw new \InvalidArgumentException('La somme des pourcentages de ventilation doit être égale à 100%.');
        }
    }

    private function validateAllocationItem(array $allocation): void
    {
        if (empty($allocation['analytical_account_id'])) {
            throw new \InvalidArgumentException('Chaque allocation doit référencer un compte analytique.');
        }

        if (! isset($allocation['percentage'])) {
            throw new \InvalidArgumentException('Chaque allocation doit contenir un pourcentage.');
        }

        $percentage = (float) $allocation['percentage'];

        if ($percentage <= 0) {
            throw new \InvalidArgumentException('Chaque pourcentage doit être strictement positif.');
        }
    }
}
