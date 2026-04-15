<?php

namespace App\Listeners;

use App\Events\PurchaseInvoiceApproved;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateDraftReceiptFromInvoice implements ShouldQueue
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
    public function handle(PurchaseInvoiceApproved $event): void
    {
        $invoice = $event->invoice;

        // Créer le reçu brouillon
        $receipt = Receipt::create([
            'invoice_id' => $invoice->id,
            'status' => 'draft',
            'receipt_date' => now(),
            'notes' => 'Reçu généré automatiquement depuis la facture ' . $invoice->number,
        ]);

        // Créer les items du reçu
        foreach ($invoice->items as $invoiceItem) {
            ReceiptItem::create([
                'receipt_id' => $receipt->id,
                'itemable_type' => $invoiceItem->itemable_type,
                'itemable_id' => $invoiceItem->itemable_id,
                'quantity' => $invoiceItem->quantity,
                'unit_price' => $invoiceItem->unit_price,
                'total' => $invoiceItem->total,
            ]);
        }
    }
}