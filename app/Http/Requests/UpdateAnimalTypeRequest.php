<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnimalTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('animal_types', 'name')->ignore($this->route('animal_type'))],
            'code' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('animal_types', 'code')->ignore($this->route('animal_type'))],
            'can_lay_eggs' => ['sometimes', 'boolean'],
            'has_growth_tracking' => ['sometimes', 'boolean'],
            'has_breeding_cycle' => ['sometimes', 'boolean'],
        ];
    }
}
