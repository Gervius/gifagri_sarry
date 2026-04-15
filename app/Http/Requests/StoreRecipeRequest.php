<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
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
            'yield' => ['required', 'numeric', 'min:0.01'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'animal_type_id' => ['nullable', 'integer', 'exists:animal_types,id'],
            'is_active' => ['sometimes', 'boolean'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'gt:0'],
            'ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
        ];
    }
}
