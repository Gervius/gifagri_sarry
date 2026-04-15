<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeightRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flock_id' => ['required', 'integer', 'exists:flocks,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'average_weight' => ['required', 'numeric', 'gt:0'],
            'sample_size' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
