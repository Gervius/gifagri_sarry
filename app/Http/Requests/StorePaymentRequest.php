<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'bank_account_id' => ['required', 'integer', 'exists:bank_accounts,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $invoiceId = $this->input('invoice_id');
            $amount = $this->input('amount');

            if (! is_numeric($invoiceId) || ! is_numeric($amount)) {
                return;
            }

            $invoice = Invoice::query()->find($invoiceId);

            if ($invoice === null) {
                return;
            }

            $paid = $invoice->payments()->sum('amount');
            $remaining = (float) $invoice->total - (float) $paid;

            if ((float) $amount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    'Le montant du paiement ne doit pas dépasser le reste à payer de la facture.'
                );
            }
        });
    }
}
