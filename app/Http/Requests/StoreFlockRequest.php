<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Flock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreFlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'animal_type_id' => ['required', 'integer', 'exists:animal_types,id'],
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'arrival_date' => ['required', 'date'],
            'initial_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('building_id')) {
                return;
            }

            $activeFlockExists = Flock::query()
                ->where('building_id', $this->input('building_id'))
                ->where('status', 'active')
                ->exists();

            if ($activeFlockExists) {
                $validator->errors()->add(
                    'building_id',
                    'Un autre lot actif existe déjà pour ce bâtiment.'
                );
            }
        });
    }
}
