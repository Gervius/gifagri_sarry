<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndFlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_reason' => ['required', 'in:sale,mortality,disease,other'],
            'notes' => ['nullable', 'string'],
            'sale_date' => ['required_if:end_reason,sale', 'date'],
            'sale_price' => ['required_if:end_reason,sale', 'numeric', 'min:0'],
            'sale_customer' => ['nullable', 'string'],
            'sale_invoice_ref' => ['nullable', 'string'],
        ];
    }
}