<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePigBreedingEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flock_id' => ['required', 'integer', 'exists:flocks,id'],
            'event_type' => ['required', 'string', Rule::in(['heat', 'mating', 'pregnancy_check', 'farrowing', 'weaning'])],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'piglets_born_alive' => ['nullable', 'integer', 'min:0'],
            'piglets_stillborn' => ['nullable', 'integer', 'min:0'],
            'piglets_weaned' => ['nullable', 'integer', 'min:0'],
            'boar_flock_id' => ['nullable', 'integer', 'exists:flocks,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $eventType = $this->input('event_type');
            $flock = \App\Models\Flock::find($this->input('flock_id'));

            // Vérifier que la flock est de type 'pig'
            if ($flock && $flock->animalType->code !== 'pig') {
                $validator->errors()->add(
                    'flock_id',
                    'Cet événement de reproduction ne peut être enregistré que pour des lots de porcs.'
                );
            }

            // Validations spécifiques par type d'événement
            match ($eventType) {
                'mating' => $this->validateMatingEvent($validator),
                'farrowing' => $this->validateFarrowingEvent($validator),
                'weaning' => $this->validateWeaningEvent($validator),
                default => null,
            };
        });
    }

    private function validateMatingEvent(Validator $validator): void
    {
        if (!$this->filled('boar_flock_id')) {
            $validator->errors()->add(
                'boar_flock_id',
                'Le verrat est obligatoire pour un événement de saillie.'
            );
        }

        $boarFlock = \App\Models\Flock::find($this->input('boar_flock_id'));
        if ($boarFlock && $boarFlock->animalType->code !== 'pig') {
            $validator->errors()->add(
                'boar_flock_id',
                'Le verrat doit être un porc.'
            );
        }
    }

    private function validateFarrowingEvent(Validator $validator): void
    {
        if (!$this->filled('piglets_born_alive') && !$this->filled('piglets_stillborn')) {
            $validator->errors()->add(
                'piglets_born_alive',
                'Au moins un nombre de porcelets nés (vivants ou morts) doit être indiqué.'
            );
        }
    }

    private function validateWeaningEvent(Validator $validator): void
    {
        if (!$this->filled('piglets_weaned') || $this->input('piglets_weaned') <= 0) {
            $validator->errors()->add(
                'piglets_weaned',
                'Le nombre de porcelets sevrés est obligatoire et doit être supérieur à zéro.'
            );
        }
    }
}
