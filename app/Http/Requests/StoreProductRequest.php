<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'stock_quantity' => ['required', 'numeric', 'min:0'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'accounting_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
        ];
    }
}
