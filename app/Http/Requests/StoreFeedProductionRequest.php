<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_id' => ['required', 'integer', 'exists:recipes,id'],
            'quantity_produced' => ['required', 'numeric', 'gt:0'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'production_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
