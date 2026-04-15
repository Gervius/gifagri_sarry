<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Delivery;
use App\Services\StockManagerService;
use Illuminate\Support\Facades\DB;

class ConfirmDeliveryAction
{
    public function __construct(
        private StockManagerService $stockManagerService,
    ) {}

    public function execute(Delivery $delivery, int $confirmerId): void
    {
        DB::transaction(function () use ($delivery, $confirmerId) {
            // Validation : vérifier que la livraison n'est pas déjà confirmée
            if ($delivery->status === 'confirmed') {
                throw new \InvalidArgumentException('Cette livraison a déjà été confirmée.');
            }

            // Validation : vérifier que la livraison est en brouillon
            if ($delivery->status !== 'draft') {
                throw new \InvalidArgumentException('Seules les livraisons en brouillon peuvent être confirmées.');
            }

            // Décrémenter le stock pour chaque item
            foreach ($delivery->items as $item) {
                $this->stockManagerService->decreaseStock(
                    $item->itemable,
                    $item->quantity,
                    'delivery_confirmed',
                    "Livraison {$delivery->id} confirmée"
                );
            }

            // Mettre à jour le statut de la livraison
            $delivery->update([
                'status' => 'confirmed',
                'confirmed_by' => $confirmerId,
                'confirmed_at' => now(),
            ]);
        });
    }
}