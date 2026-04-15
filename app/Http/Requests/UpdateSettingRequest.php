<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('settings', 'key')->ignore($this->route('setting'))],
            'value' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'type' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
