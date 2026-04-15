<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\FeedProduction;
use App\Models\Recipe;
use App\Services\StockManagerService;
use Illuminate\Support\Facades\DB;

class ApproveFeedProductionAction
{
    public function __construct(
        private StockManagerService $stockManager,
    ) {}

    public function execute(FeedProduction $feedProduction, int $approverId): void
    {
        DB::transaction(function () use ($feedProduction, $approverId): void {
            if ($feedProduction->status !== 'draft') {
                throw new \InvalidArgumentException('Seules les productions en brouillon peuvent être approuvées.');
            }

            /** @var Recipe $recipe */
            $recipe = $feedProduction->recipe()->with('recipeIngredients.ingredient')->firstOrFail();
            $multiplier = $this->computeMultiplier($recipe->yield, $feedProduction->quantity_produced);
            $totalCost = 0.0;

            foreach ($recipe->recipeIngredients as $ingredientLine) {
                $ingredient = $ingredientLine->ingredient;
                if ($ingredient === null) {
                    continue;
                }

                $consumedQuantity = round($ingredientLine->quantity * $multiplier, 2);
                if ($consumedQuantity <= 0) {
                    continue;
                }

                $this->stockManager->decreaseStock(
                    $ingredient,
                    $consumedQuantity,
                    'feed_production',
                    "Consommation de {$ingredient->name} pour la production #{$feedProduction->id}"
                );

                $totalCost += round($consumedQuantity * (float) $ingredient->pmp, 2);
            }

            if ($feedProduction->quantity_produced <= 0) {
                throw new \InvalidArgumentException('La quantité produite doit être supérieure à zéro.');
            }

            $unitCost = round($totalCost / $feedProduction->quantity_produced, 2);

            $this->stockManager->increaseFeedStock(
                $recipe->id,
                $feedProduction->unit_id,
                $feedProduction->quantity_produced,
                $unitCost,
                'feed_production',
                $feedProduction,
                $approverId,
            );

            $feedProduction->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
        });
    }

    private function computeMultiplier(float $yield, float $produced): float
    {
        if ($yield <= 0.0) {
            return 1.0;
        }

        return $produced / $yield;
    }
}
