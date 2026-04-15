<?php

namespace App\Http\Controllers;

use App\Http\Resources\OverdueInvoiceResource;
use App\Http\Resources\PartnerBalanceResource;
use App\Services\PartnerBalanceService;
use Illuminate\Http\Request;

/**
 * Contrôleur pour la gestion des créances et dettes
 */
class DebtCollectionController extends Controller
{
    public function __construct(
        private PartnerBalanceService $balanceService
    ) {}

    /**
     * Retourne la liste des restes à recouvrer par partenaire
     */
    public function index(Request $request)
    {
        $balances = $this->balanceService->getAllPartnerBalances();

        return PartnerBalanceResource::collection($balances);
    }

    /**
     * Retourne les factures en retard pour un type donné
     */
    public function overdue(Request $request)
    {
        $type = $request->query('type', 'sale'); // 'sale' ou 'purchase'

        $invoices = $this->balanceService->getOverdueInvoices($type);

        return OverdueInvoiceResource::collection($invoices);
    }
}