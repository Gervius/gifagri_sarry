<?php

namespace App\Listeners;

use App\Events\SaleInvoiceApproved;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateDraftDeliveryFromInvoice implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaleInvoiceApproved $event): void
    {
        $invoice = $event->invoice;

        // Créer la livraison brouillon
        $delivery = Delivery::create([
            'invoice_id' => $invoice->id,
            'status' => 'draft',
            'delivery_date' => now(),
            'notes' => 'Livraison générée automatiquement depuis la facture ' . $invoice->number,
        ]);

        // Créer les items de livraison
        foreach ($invoice->items as $invoiceItem) {
            DeliveryItem::create([
                'delivery_id' => $delivery->id,
                'itemable_type' => $invoiceItem->itemable_type,
                'itemable_id' => $invoiceItem->itemable_id,
                'quantity' => $invoiceItem->quantity,
                'unit_price' => $invoiceItem->unit_price,
                'total' => $invoiceItem->total,
            ]);
        }
    }
}
