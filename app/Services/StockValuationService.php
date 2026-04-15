<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ingredient;
use App\Models\LandedCostAllocation;
use App\Models\PmpHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Service de valorisation des stocks et calcul du prix moyen pondéré (PMP).
 */
class StockValuationService
{
    /**
     * Calcule le nouveau PMP et l'applique sur l'ingrédient.
     *
     * @param Ingredient $ingredient        L'ingrédient reçu
     * @param float      $addedQuantity     Quantité ajoutée lors de la réception
     * @param float      $netPurchasePrice  Prix d'achat net total de l'entrée
     * @param array      $landedCostItemIds Identifiants des allocations de frais d'approche
     * @param Model      $source           Modèle source du mouvement (par ex. Receipt)
     */
    public function calculateAndApplyNewPMP(
        Ingredient $ingredient,
        float $addedQuantity,
        float $netPurchasePrice,
        array $landedCostItemIds = [],
        Model $source,
    ): void {
        DB::transaction(function () use (
            $ingredient,
            $addedQuantity,
            $netPurchasePrice,
            $landedCostItemIds,
            $source,
        ): void {
            $existingStock = (float) $ingredient->current_stock;
            $oldPmp = (float) $ingredient->pmp;

            $landedCostTotal = (float) LandedCostAllocation::query()
                ->whereIn('id', $landedCostItemIds)
                ->sum('allocated_amount');

            $totalAcquisitionCost = $netPurchasePrice + $landedCostTotal;
            $existingStockValue = $existingStock * $oldPmp;
            $totalQuantity = $existingStock + $addedQuantity;

            $newPmp = $totalQuantity > 0.0
                ? ($existingStockValue + $totalAcquisitionCost) / $totalQuantity
                : $oldPmp;

            if (! is_finite($newPmp) || $newPmp < 0.0) {
                $newPmp = $oldPmp;
            }

            PmpHistory::create([
                'ingredient_id' => $ingredient->id,
                'date' => now()->toDateString(),
                'old_pmp' => $oldPmp,
                'new_pmp' => $newPmp,
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
            ]);

            $ingredient->forceFill(['pmp' => $newPmp])->save();
        });
    }
}
