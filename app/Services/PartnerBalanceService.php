<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service pour gérer les balances partenaires (créances clients et dettes fournisseurs)
 */
class PartnerBalanceService
{
    /**
     * Retourne l'historique financier d'un partenaire : factures, paiements et balance
     *
     * @param int $partnerId
     * @return array
     */
    public function getPartnerStatement(int $partnerId): array
    {
        // Récupérer le partenaire
        $partner = Partner::findOrFail($partnerId);

        // Récupérer les factures avec paiements
        $invoices = Invoice::with(['payments', 'items'])
            ->where('partner_id', $partnerId)
            ->orderBy('date', 'desc')
            ->get();

        // Calculer la balance totale via requête SQL
        $balance = $this->calculatePartnerBalance($partnerId);

        return [
            'partner' => $partner,
            'invoices' => $invoices,
            'total_balance' => $balance,
        ];
    }

    /**
     * Retourne les factures en retard pour un type donné
     *
     * @param string $type 'sale' ou 'purchase'
     * @return Collection
     */
    public function getOverdueInvoices(string $type): Collection
    {
        return Invoice::with(['partner', 'payments'])
            ->where('type', $type)
            ->where('due_date', '<', now())
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Calcule la balance totale d'un partenaire via requête SQL
     *
     * @param int $partnerId
     * @return float
     */
    private function calculatePartnerBalance(int $partnerId): float
    {
        $result = DB::table('invoices')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.partner_id', $partnerId)
            ->whereNull('invoices.deleted_at') // Respecter soft deletes
            ->selectRaw('
                SUM(invoices.total) as total_invoiced,
                COALESCE(SUM(payments.amount), 0) as total_paid
            ')
            ->first();

        return $result->total_invoiced - $result->total_paid;
    }

    /**
     * Retourne les balances restantes par partenaire pour les créances et dettes
     *
     * @return Collection
     */
    public function getAllPartnerBalances(): Collection
    {
        return DB::table('partners')
            ->leftJoin('invoices', 'partners.id', '=', 'invoices.partner_id')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->whereNull('partners.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->selectRaw('
                partners.id,
                partners.name,
                partners.type as partner_type,
                SUM(CASE WHEN invoices.type = \'sale\' THEN invoices.total ELSE 0 END) as total_sales,
                SUM(CASE WHEN invoices.type = \'purchase\' THEN invoices.total ELSE 0 END) as total_purchases,
                COALESCE(SUM(payments.amount), 0) as total_paid,
                (SUM(CASE WHEN invoices.type = \'sale\' THEN invoices.total ELSE 0 END) - COALESCE(SUM(payments.amount), 0)) as receivables_balance,
                (SUM(CASE WHEN invoices.type = \'purchase\' THEN invoices.total ELSE 0 END) - COALESCE(SUM(payments.amount), 0)) as payables_balance
            ')
            ->groupBy('partners.id', 'partners.name', 'partners.type')
            ->havingRaw('(SUM(CASE WHEN invoices.type = \'sale\' THEN invoices.total ELSE 0 END) - COALESCE(SUM(payments.amount), 0)) != 0 OR (SUM(CASE WHEN invoices.type = \'purchase\' THEN invoices.total ELSE 0 END) - COALESCE(SUM(payments.amount), 0)) != 0')
            ->orderBy('partners.name')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'partner_id' => $item->id,
                    'partner_name' => $item->name,
                    'partner_type' => $item->partner_type,
                    'receivables_balance' => (float) $item->receivables_balance,
                    'payables_balance' => (float) $item->payables_balance,
                ];
            });
    }
}