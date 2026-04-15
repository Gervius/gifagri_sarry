<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Receipt;
use App\Services\StockManagerService;
use Illuminate\Support\Facades\DB;

class ConfirmReceiptAction
{
    public function __construct(
        private StockManagerService $stockManagerService,
    ) {}

    public function execute(Receipt $receipt, int $confirmerId): void
    {
        DB::transaction(function () use ($receipt, $confirmerId) {
            // Validation : vérifier que le reçu n'est pas déjà confirmé
            if ($receipt->status === 'confirmed') {
                throw new \InvalidArgumentException('Ce reçu a déjà été confirmé.');
            }

            // Validation : vérifier que le reçu est en brouillon
            if ($receipt->status !== 'draft') {
                throw new \InvalidArgumentException('Seuls les reçus en brouillon peuvent être confirmés.');
            }

            // Augmenter le stock pour chaque item
            foreach ($receipt->items as $item) {
                $this->stockManagerService->increaseStock(
                    $item->itemable,
                    $item->quantity,
                    'receipt_confirmed',
                    "Reçu {$receipt->id} confirmé"
                );
            }

            // Mettre à jour le statut du reçu
            $receipt->update([
                'status' => 'confirmed',
                'confirmed_by' => $confirmerId,
                'confirmed_at' => now(),
            ]);
        });
    }
}