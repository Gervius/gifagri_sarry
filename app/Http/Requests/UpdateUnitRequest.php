<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'symbol' => ['sometimes', 'required', 'string', 'max:50'],
            'type' => ['sometimes', 'required', 'string', 'in:mass,volume,unit,length'],
            'conversion_factor' => ['sometimes', 'nullable', 'numeric', 'gte:0'],
            'base_unit_id' => ['sometimes', 'nullable', 'integer', 'exists:units,id'],
        ];
    }
}
