<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:animal_types,name'],
            'code' => ['required', 'string', 'max:100', 'unique:animal_types,code'],
            'can_lay_eggs' => ['sometimes', 'boolean'],
            'has_growth_tracking' => ['sometimes', 'boolean'],
            'has_breeding_cycle' => ['sometimes', 'boolean'],
        ];
    }
}
