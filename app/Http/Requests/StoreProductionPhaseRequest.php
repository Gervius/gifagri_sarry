<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionPhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'animal_type_id' => ['required', 'integer', 'exists:animal_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer'],
            'typical_duration_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'default_recipe_id' => ['sometimes', 'nullable', 'integer', 'exists:recipes,id'],
        ];
    }
}
