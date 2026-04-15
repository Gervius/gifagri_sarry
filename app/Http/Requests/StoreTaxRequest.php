<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'gte:0', 'lte:100'],
            'accounting_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
