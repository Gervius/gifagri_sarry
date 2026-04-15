<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEggCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'weight_min_grams' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'weight_max_grams' => ['sometimes', 'nullable', 'integer', 'min:0', 'gte:weight_min_grams'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
