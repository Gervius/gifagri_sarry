<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['sale', 'purchase'])],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id', 'required_without:customer_name'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('items') && is_string($this->input('items'))) {
            $items = json_decode($this->input('items'), true);

            if (is_array($items)) {
                $this->merge(['items' => $items]);
            }
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('partner_id') && ! $this->filled('customer_name')) {
                $validator->errors()->add(
                    'partner_id',
                    'Le partenaire ou le nom du client est requis.'
                );
            }

            $items = $this->input('items');

            if (! is_array($items) || count($items) === 0) {
                $validator->errors()->add('items', 'La facture doit contenir au moins un article.');
            }
        });
    }
}
