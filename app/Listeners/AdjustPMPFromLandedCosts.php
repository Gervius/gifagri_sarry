<?php

namespace App\Listeners;

use App\Events\InvoiceApproved;
use App\Models\Ingredient;
use App\Models\LandedCostAllocation;
use App\Services\StockValuationService;

class AdjustPMPFromLandedCosts
{
    public function __construct(
        private StockValuationService $stockValuationService,
    ) {}

    public function handle(InvoiceApproved $event): void
    {
        // Seules les factures d'achat affectent le PMP
        if ($event->invoice->type !== 'purchase') {
            return;
        }

        foreach ($event->invoice->items as $invoiceItem) {
            // Vérifier si l'item concerne un ingrédient
            if (!($invoiceItem->itemable instanceof Ingredient)) {
                continue;
            }

            $ingredient = $invoiceItem->itemable;
            $quantity = $invoiceItem->quantity;
            $unitPrice = $invoiceItem->unit_price;

            // Prix d'achat net total (avant frais)
            $netPurchasePrice = (float) ($quantity * $unitPrice);

            // Récupérer les allocations de frais pour cet item
            $landedCostAllocations = LandedCostAllocation::query()
                ->where('invoice_item_id', $invoiceItem->id)
                ->get();

            $landedCostAllocationIds = $landedCostAllocations->pluck('id')->toArray();

            // Calculer et appliquer le nouveau PMP
            $this->stockValuationService->calculateAndApplyNewPMP(
                ingredient: $ingredient,
                addedQuantity: (float) $quantity,
                netPurchasePrice: $netPurchasePrice,
                landedCostItemIds: $landedCostAllocationIds,
                source: $event->invoice,
            );
        }
    }
}
