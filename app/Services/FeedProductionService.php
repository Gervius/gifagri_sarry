<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FeedProduction;
use App\Models\FeedStock;
use App\Models\FeedStockMovement;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class FeedProductionService
{
    public function approveProduction(FeedProduction $feedProduction): void
    {
        $recipe = $feedProduction->recipe()->with('recipeIngredients.ingredient')->firstOrFail();
        $multiplier = $this->computeMultiplier($recipe->yield, $feedProduction->quantity_produced);

        foreach ($recipe->recipeIngredients as $recipeIngredient) {
            $quantity = round((float) $recipeIngredient->quantity * $multiplier, 2);

            if ($quantity <= 0.0) {
                continue;
            }

            $ingredient = $recipeIngredient->ingredient;

            if ($ingredient !== null) {
                $ingredient->decrement('current_stock', $quantity);
            }

            StockMovement::create([
                'ingredient_id' => $recipeIngredient->ingredient_id,
                'type' => 'out',
                'quantity' => $quantity,
                'unit_id' => $recipeIngredient->unit_id,
                'base_quantity' => $quantity,
                'base_unit_id' => $recipeIngredient->unit_id,
                'unit_price' => null,
                'reason' => 'Consommation des ingrédients pour production de nourriture',
                'source_type' => $feedProduction::class,
                'source_id' => $feedProduction->id,
                'status' => 'approved',
                'created_by' => Auth::id() ?? $feedProduction->created_by,
                'approved_by' => Auth::id() ?? $feedProduction->created_by,
                'approved_at' => now(),
            ]);
        }

        $feedStock = FeedStock::query()
            ->firstWhere([
                'recipe_id' => $feedProduction->recipe_id,
                'unit_id' => $feedProduction->unit_id,
            ]);

        if ($feedStock === null) {
            $feedStock = FeedStock::create([
                'recipe_id' => $feedProduction->recipe_id,
                'quantity' => $feedProduction->quantity_produced,
                'unit_id' => $feedProduction->unit_id,
                'unit_cost' => null,
            ]);
        } else {
            $feedStock->increment('quantity', $feedProduction->quantity_produced);
        }

        FeedStockMovement::create([
            'feed_stock_id' => $feedStock->id,
            'type' => 'in',
            'quantity' => $feedProduction->quantity_produced,
            'unit_price' => $feedStock->unit_cost ?? 0.0,
            'source_type' => $feedProduction::class,
            'source_id' => $feedProduction->id,
            'created_by' => Auth::id() ?? $feedProduction->created_by,
        ]);
    }

    private function computeMultiplier(float $yield, float $produced): float
    {
        return $yield > 0.0 ? $produced / $yield : 1.0;
    }
}
