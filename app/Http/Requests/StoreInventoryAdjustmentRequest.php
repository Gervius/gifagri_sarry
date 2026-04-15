<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\FeedStock;
use App\Models\Ingredient;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stockable_type' => ['required', 'string', Rule::in(['ingredient', 'feed_stock'])],
            'stockable_id' => ['required', 'integer'],
            'actual_quantity' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = $this->input('stockable_type');
            $id = $this->input('stockable_id');

            if (! in_array($type, ['ingredient', 'feed_stock'], true)) {
                return;
            }

            if (! is_numeric($id)) {
                $validator->errors()->add('stockable_id', 'L\'identifiant de l\'entité stockable doit être un entier valide.');
                return;
            }

            $exists = match ($type) {
                'ingredient' => Ingredient::query()->where('id', $id)->exists(),
                'feed_stock' => FeedStock::query()->where('id', $id)->exists(),
                default => false,
            };

            if (! $exists) {
                $validator->errors()->add(
                    'stockable_id',
                    'L\'entité stockable spécifiée est invalide ou n\'existe pas.'
                );
            }
        });
    }
}
