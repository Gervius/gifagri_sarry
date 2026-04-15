<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AccountingImbalanceException;
use App\Exceptions\AccountingRuleNotFoundException;
use App\Models\AccountingRule;
use App\Models\AccountingRuleLine;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalVoucher;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountingEngineService
{
    public function generateEntriesFromInvoice(Invoice $invoice, string $eventType): JournalVoucher
    {
        return DB::transaction(function () use ($invoice, $eventType): JournalVoucher {
            $rule = AccountingRule::query()
                ->with('accountingRuleLines.account')
                ->where('event_type', $eventType)
                ->where('is_active', true)
                ->first();

            if ($rule === null) {
                throw new AccountingRuleNotFoundException($eventType);
            }

            $journalVoucher = JournalVoucher::create([
                'date' => $invoice->date->toDateString(),
                'description' => sprintf(
                    'Pièce comptable générée pour la facture %s',
                    $invoice->number ?? $invoice->id,
                ),
                'source_type' => $invoice::class,
                'source_id' => $invoice->getKey(),
                'created_by' => $invoice->created_by,
            ]);

            foreach ($rule->accountingRuleLines as $line) {
                $amount = $this->resolveLineAmount($invoice, $line);
                $amount = round($amount * ((float) $line->percentage) / 100.0, 2);

                if ($amount <= 0.0) {
                    continue;
                }

                $accountId = $this->resolveAccountId($invoice, $line);

                JournalEntry::create([
                    'journal_voucher_id' => $journalVoucher->id,
                    'account_id' => $accountId,
                    'debit' => $line->type === 'debit' ? $amount : 0,
                    'credit' => $line->type === 'credit' ? $amount : 0,
                    'description' => $this->renderEntryDescription($invoice, $line, $amount),
                ]);
            }

            $this->assertBalanced($journalVoucher);

            return $journalVoucher;
        });
    }

    private function resolveLineAmount(Invoice $invoice, AccountingRuleLine $line): float
    {
        return match ($line->amount_source) {
            'total_ht' => (float) $invoice->subtotal,
            'tax_amount' => (float) $invoice->tax_amount,
            'total_ttc' => (float) $invoice->total,
            default => throw new RuntimeException(
                "Source de montant inconnue '{$line->amount_source}' pour la règle comptable."
            ),
        };
    }

    private function resolveAccountId(Invoice $invoice, AccountingRuleLine $line): int
    {
        if ($line->account_resolution_type === 'fixed') {
            if ($line->account_id === null) {
                throw new RuntimeException('La ligne de règle comptable fixe doit définir un account_id.');
            }

            return $line->account_id;
        }

        if ($line->account_resolution_type === 'dynamic') {
            return $this->resolveDynamicAccountId($invoice, $line);
        }

        throw new RuntimeException(
            "Type de résolution de compte inconnu '{$line->account_resolution_type}'."
        );
    }

    private function resolveDynamicAccountId(Invoice $invoice, AccountingRuleLine $line): int
    {
        if ($line->dynamic_account_placeholder === null) {
            throw new RuntimeException('Le placeholder de compte dynamique est requis pour la ligne de règle.');
        }

        $placeholder = strtolower($line->dynamic_account_placeholder);

        if (str_contains($placeholder, 'partner')) {
            return $this->resolvePartnerAccountId($invoice);
        }

        if (str_contains($placeholder, 'tax')) {
            return $this->resolveTaxAccountId($invoice);
        }

        if (str_contains($placeholder, 'product') || str_contains($placeholder, 'item')) {
            return $this->resolveInvoiceItemAccountId($invoice);
        }

        throw new RuntimeException(
            "Placeholder de compte dynamique inconnu '{$line->dynamic_account_placeholder}'."
        );
    }

    private function resolvePartnerAccountId(Invoice $invoice): int
    {
        $partner = $invoice->partner;
        $accountId = data_get($partner, 'accounting_account_id') ?? data_get($partner, 'account_id');

        if ($accountId === null) {
            throw new RuntimeException('Impossible de résoudre le compte du partenaire de la facture.');
        }

        return (int) $accountId;
    }

    private function resolveTaxAccountId(Invoice $invoice): int
    {
        $tax = $invoice->taxes()->first();

        if ($tax === null || $tax->accounting_account_id === null) {
            throw new RuntimeException('Impossible de résoudre le compte de taxe pour la facture.');
        }

        return (int) $tax->accounting_account_id;
    }

    private function resolveInvoiceItemAccountId(Invoice $invoice): int
    {
        $item = $invoice->items()->with('itemable')->first();
        $itemable = $item?->itemable;
        $accountId = data_get($itemable, 'accounting_account_id') ?? data_get($itemable, 'account_id');

        if ($accountId === null) {
            throw new RuntimeException('Impossible de résoudre le compte de l’article de la facture.');
        }

        return (int) $accountId;
    }

    private function renderEntryDescription(Invoice $invoice, AccountingRuleLine $line, float $amount): ?string
    {
        if (empty($line->description_template)) {
            return null;
        }

        return strtr($line->description_template, [
            '{{invoice_number}}' => (string) ($invoice->number ?? $invoice->id),
            '{{partner_name}}' => (string) $invoice->partner?->name,
            '{{amount}}' => number_format($amount, 2, '.', ''),
        ]);
    }

    private function assertBalanced(JournalVoucher $journalVoucher): void
    {
        $totals = $journalVoucher->journalEntries()
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        if ($totals === null) {
            throw new AccountingImbalanceException($journalVoucher->id, 0.0, 0.0);
        }

        $totalDebit = (string) $totals->total_debit;
        $totalCredit = (string) $totals->total_credit;

        if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
            throw new AccountingImbalanceException(
                $journalVoucher->id,
                (float) $totalDebit,
                (float) $totalCredit,
            );
        }
    }
}
