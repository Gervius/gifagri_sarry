<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_number' => ['sometimes', 'required', 'string', 'max:255'],
            'manufacturing_date' => ['sometimes', 'nullable', 'date', 'before_or_equal:today'],
            'expiration_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:manufacturing_date'],
            'initial_quantity' => ['sometimes', 'required', 'numeric', 'gte:0'],
            'current_quantity' => ['sometimes', 'nullable', 'numeric', 'gte:0'],
            'batchable_type' => ['sometimes', 'required', 'string', 'max:255'],
            'batchable_id' => ['sometimes', 'required', 'integer'],
        ];
    }
}
