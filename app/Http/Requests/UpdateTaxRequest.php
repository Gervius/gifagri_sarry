<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'rate' => ['sometimes', 'required', 'numeric', 'gte:0', 'lte:100'],
            'accounting_account_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
