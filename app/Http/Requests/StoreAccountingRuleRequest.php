<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\AccountingRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAccountingRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', AccountingRule::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
