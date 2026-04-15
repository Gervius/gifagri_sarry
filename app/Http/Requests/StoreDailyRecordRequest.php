<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Batch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDailyRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'flock_id' => ['required', 'integer', 'exists:flocks,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'losses' => ['required', 'integer', 'min:0'],
            'eggs' => ['required', 'integer', 'min:0'],
            'feed_consumed' => ['required', 'numeric', 'min:0'],
            'feed_type_id' => [
                'nullable',
                'integer',
                'exists:recipes,id',
                function (string $attribute, $value, callable $fail): void {
                    $feedConsumed = $this->input('feed_consumed', 0);

                    if (is_numeric($feedConsumed) && (float) $feedConsumed > 0.0 && empty($value)) {
                        $fail('Le type de ration est obligatoire lorsque la consommation de nourriture est supérieure à zéro.');
                    }
                },
            ],
            'water_consumed' => ['required', 'numeric', 'min:0'],
            'feed_batch_id' => ['nullable', 'integer', 'exists:batches,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $feedBatchId = $this->input('feed_batch_id');

            if (! empty($feedBatchId)) {
                $batch = Batch::query()->find($feedBatchId);

                if ($batch !== null && $batch->expiration_date !== null && $batch->expiration_date->isPast()) {
                    $validator->errors()->add(
                        'feed_batch_id',
                        'Le lot sélectionné est expiré et ne peut pas être utilisé.'
                    );
                }
            }

            $flockId = $this->input('flock_id');
            $eggs = $this->input('eggs', 0);

            if (! empty($flockId)) {
                $flock = \App\Models\Flock::find($flockId);
                if ($flock && in_array($flock->animalType->code, ['broiler', 'pig']) && $eggs > 0) {
                    $validator->errors()->add(
                        'eggs',
                        'Les ' . ($flock->animalType->code === 'broiler' ? 'poulets de chair' : 'porcs') . ' ne pondent pas d\'œufs.'
                    );
                }
            }
        });
    }
}
