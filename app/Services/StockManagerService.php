<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FeedStock;
use App\Models\FeedStockMovement;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockManagerService
{
    /**
     * Augmente le stock d'un élément stockable.
     */
    public function increaseStock(Model $stockable, float $quantity, string $reason, string $notes = null): void
    {
        // Validation : vérifier que l'élément est stockable
        if (!$this->isStockable($stockable)) {
            throw new \InvalidArgumentException('Cet élément n\'est pas stockable.');
        }

        // Validation : quantité positive
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('La quantité doit être positive.');
        }

        $field = $this->stockField($stockable);

        $stockable->increment($field, $quantity);

        StockMovement::create([
            'stockable_type' => get_class($stockable),
            'stockable_id' => $stockable->id,
            'quantity' => $quantity,
            'type' => 'increase',
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }

    /**
     * Diminue le stock d'un élément stockable.
     */
    public function decreaseStock(Model $stockable, float $quantity, string $reason, string $notes = null): void
    {
        // Validation : vérifier que l'élément est stockable
        if (!$this->isStockable($stockable)) {
            throw new \InvalidArgumentException('Cet élément n\'est pas stockable.');
        }

        // Validation : quantité positive
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('La quantité doit être positive.');
        }

        $field = $this->stockField($stockable);

        if ($stockable->{$field} < $quantity) {
            throw new \InvalidArgumentException('Stock insuffisant pour cet élément.');
        }

        $stockable->decrement($field, $quantity);

        StockMovement::create([
            'stockable_type' => get_class($stockable),
            'stockable_id' => $stockable->id,
            'quantity' => -$quantity,
            'type' => 'decrease',
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }

    /**
     * Créditer le stock de feed produit et enregistrer le coût de revient.
     */
    public function increaseFeedStock(int $recipeId, int $unitId, float $quantity, float $unitCost, string $reason, ?Model $source = null, ?int $createdBy = null): FeedStock
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('La quantité de feed produite doit être positive.');
        }

        if ($unitCost < 0) {
            throw new \InvalidArgumentException('Le coût unitaire doit être positif ou nul.');
        }

        $feedStock = FeedStock::firstWhere([
            'recipe_id' => $recipeId,
            'unit_id' => $unitId,
        ]);

        if ($feedStock === null) {
            $feedStock = FeedStock::create([
                'recipe_id' => $recipeId,
                'quantity' => $quantity,
                'unit_id' => $unitId,
                'unit_cost' => round($unitCost, 2),
            ]);
        } else {
            $existingQuantity = (float) $feedStock->quantity;
            $existingUnitCost = (float) ($feedStock->unit_cost ?? 0.0);
            $newQuantity = $existingQuantity + $quantity;
            $feedStock->quantity = $newQuantity;
            $feedStock->unit_cost = $newQuantity > 0
                ? round((($existingQuantity * $existingUnitCost) + ($quantity * $unitCost)) / $newQuantity, 2)
                : round($unitCost, 2);
            $feedStock->save();
        }

        FeedStockMovement::create([
            'feed_stock_id' => $feedStock->id,
            'type' => 'in',
            'quantity' => $quantity,
            'unit_price' => round($unitCost, 2),
            'source_type' => $source?->getMorphClass(),
            'source_id' => $source?->getKey(),
            'created_by' => $createdBy ?? Auth::id(),
        ]);

        return $feedStock;
    }

    /**
     * Détermine le champ de stock disponible sur le modèle.
     */
    private function stockField(Model $model): string
    {
        $schema = $model->getConnection()->getSchemaBuilder();

        if ($schema->hasColumn($model->getTable(), 'current_quantity')) {
            return 'current_quantity';
        }

        if ($schema->hasColumn($model->getTable(), 'current_stock')) {
            return 'current_stock';
        }

        throw new \InvalidArgumentException('Cet élément n\'est pas stockable.');
    }
}