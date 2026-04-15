<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProphylaxisPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'animal_type_id' => ['required', 'integer', 'exists:animal_types,id'],
            'description' => ['nullable', 'string'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.day_of_age' => ['required', 'integer', 'min:0'],
            'steps.*.treatment_type' => ['required', 'string', 'max:255'],
            'steps.*.administration_method' => ['required', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string'],
        ];
    }
}
