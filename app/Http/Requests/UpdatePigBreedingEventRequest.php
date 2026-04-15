<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePigBreedingEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flock_id' => ['sometimes', 'required', 'integer', 'exists:flocks,id'],
            'event_type' => ['sometimes', 'required', 'string', 'max:255'],
            'event_date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'piglets_born_alive' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'piglets_stillborn' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'piglets_weaned' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'boar_flock_id' => ['sometimes', 'nullable', 'integer', 'exists:flocks,id'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
