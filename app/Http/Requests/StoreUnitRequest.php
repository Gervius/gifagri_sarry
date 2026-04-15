<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:mass,volume,unit,length'],
            'conversion_factor' => ['sometimes', 'numeric', 'gte:0'],
            'base_unit_id' => ['sometimes', 'nullable', 'integer', 'exists:units,id'],
        ];
    }
}
