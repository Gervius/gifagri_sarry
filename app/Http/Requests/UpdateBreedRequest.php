<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBreedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'animal_type_id' => ['sometimes', 'required', 'integer', 'exists:animal_types,id'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
