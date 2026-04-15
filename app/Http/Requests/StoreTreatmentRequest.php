<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_date' => ['required', 'date', 'before_or_equal:today'],
            'treatment_type' => ['required', 'string', 'max:255'],
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
        ];
    }
}
