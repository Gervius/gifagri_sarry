<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankAccountRequest extends FormRequest
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
            'account_number' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('bank_accounts', 'account_number')->ignore($this->route('bank_account')),
            ],
            'accounting_account_id' => ['sometimes', 'required', 'integer', 'exists:accounts,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
