<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\AccountingRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountingRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('accounting_rule')) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'event_type' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
