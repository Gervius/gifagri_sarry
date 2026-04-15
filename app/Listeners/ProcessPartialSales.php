<?php

namespace App\Listeners;

use App\Events\InvoiceApproved;
use App\Services\StockManagerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessPartialSales
{
    public function __construct(
        private StockManagerService $stockManagerService,
    ) {}

    public function handle(InvoiceApproved $event): void
    {
        if ($event->invoice->type !== 'sale') {
            return;
        }

        foreach ($event->invoice->items as $item) {
            if ($item->itemable instanceof \App\Models\Flock) {
                $flock = $item->itemable;
                if ($flock->animalType->code === 'broiler') {
                    $this->stockManagerService->decreaseStock(
                        $flock,
                        $item->quantity,
                        'partial_sale',
                        "Vente partielle depuis la facture {$event->invoice->id}"
                    );
                }
            }
        }
    }
}
