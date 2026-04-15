<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectFlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // L'autorisation est faite dans le contrôleur via Policy
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
        ];
    }
}