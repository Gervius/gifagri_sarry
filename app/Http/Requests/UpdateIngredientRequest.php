<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ingredient = $this->route('ingredient');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients', 'name')->ignore($ingredient),
            ],
            'reference' => ['nullable', 'string', 'max:255'],
            'default_unit_id' => ['required', 'integer', 'exists:units,id'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'min:0'],
            'pmp' => ['nullable', 'numeric', 'min:0'],
            'partner_id' => ['nullable', 'integer', 'exists:partners,id'],
            'is_active' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
