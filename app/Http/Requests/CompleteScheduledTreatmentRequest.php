<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteScheduledTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:done'],
            'actual_treatment_id' => [
                Rule::requiredIf($this->input('status') === 'done'),
                'nullable',
                'integer',
                'exists:treatments,id',
            ],
        ];
    }
}
