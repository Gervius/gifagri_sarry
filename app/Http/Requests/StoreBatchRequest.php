<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_number' => ['required', 'string', 'max:255'],
            'manufacturing_date' => ['nullable', 'date', 'before_or_equal:today'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:manufacturing_date'],
            'initial_quantity' => ['required', 'numeric', 'gte:0'],
            'current_quantity' => ['nullable', 'numeric', 'gte:0'],
            'batchable_type' => ['required', 'string', 'max:255'],
            'batchable_id' => ['required', 'integer'],
        ];
    }
}
