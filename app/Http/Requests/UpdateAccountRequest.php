<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('accounts', 'code')->ignore($this->route('account')),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
