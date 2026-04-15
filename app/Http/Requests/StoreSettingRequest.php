<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:settings,key'],
            'value' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
