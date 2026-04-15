<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Flock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateFlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $flock = $this->route('flock');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('flocks', 'name')->ignore($flock),
            ],
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

            $flock = $this->route('flock');

            $activeFlockExists = Flock::query()
                ->where('building_id', $this->input('building_id'))
                ->where('status', 'active')
                ->when($flock !== null, fn ($query) => $query->where('id', '!=', $flock->id))
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
