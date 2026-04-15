<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CompanySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_rccm' => ['required', 'string', 'max:255'],
            'company_ifu' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string', 'max:1000'],
            'company_phone' => ['required', 'string', 'max:50'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_logo' => ['sometimes', 'nullable', 'image', 'max:4096'],
        ];
    }
}
