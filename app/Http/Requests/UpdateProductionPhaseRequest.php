<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductionPhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'animal_type_id' => ['sometimes', 'required', 'integer', 'exists:animal_types,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'order' => ['sometimes', 'required', 'integer'],
            'typical_duration_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'default_recipe_id' => ['sometimes', 'nullable', 'integer', 'exists:recipes,id'],
        ];
    }
}
