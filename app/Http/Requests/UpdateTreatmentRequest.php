<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'veterinarian' => ['sometimes', 'nullable', 'string', 'max:255'],
            'treatment_type' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'gte:0'],
            'invoice_reference' => ['sometimes', 'nullable', 'string', 'max:255'],
            'batch_id' => ['sometimes', 'nullable', 'integer', 'exists:batches,id'],
        ];
    }
}
