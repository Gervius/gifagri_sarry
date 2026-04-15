<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
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
            'account_number' => ['required', 'string', 'max:100', 'unique:bank_accounts,account_number'],
            'accounting_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
