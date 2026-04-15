<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\InvoiceApproved;
use App\Events\PurchaseInvoiceApproved;
use App\Events\SaleInvoiceApproved;
use App\Models\Invoice;
use App\Services\AccountingEngineService;
use Illuminate\Support\Facades\DB;

class ApproveInvoiceAction
{
    public function __construct(
        private AccountingEngineService $accountingEngineService,
    ) {}

    public function execute(Invoice $invoice, int $approverId): void
    {
        DB::transaction(function () use ($invoice, $approverId) {
            // Validation : vérifier que l'invoice n'est pas déjà approuvée
            if ($invoice->status === 'approved') {
                throw new \InvalidArgumentException('Cette facture a déjà été approuvée.');
            }

            // Déterminer le type d'événement comptable
            $eventType = match ($invoice->type) {
                'purchase' => 'purchase_invoice_validated',
                'sale' => 'sale_invoice_validated',
                default => throw new \UnexpectedValueException("Type de facture inconnu: {$invoice->type}"),
            };

            // Générer les écritures comptables
            $this->accountingEngineService->generateEntriesFromInvoice($invoice, $eventType);

            // Mettre à jour le statut de la facture
            $invoice->update([
                'status' => 'approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            // Dispatcher l'événement pour les listeners
            InvoiceApproved::dispatch($invoice);

            // Dispatcher l'événement spécifique aux achats
            if ($invoice->type === 'purchase') {
                PurchaseInvoiceApproved::dispatch($invoice);
            }

            // Dispatcher l'événement spécifique aux ventes
            if ($invoice->type === 'sale') {
                SaleInvoiceApproved::dispatch($invoice);
            }
        });
    }
}
