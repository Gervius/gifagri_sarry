<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveRequest extends FormRequest
{
    public function authorize(): bool
    {
        $entity = $this->route('daily_record')
            ?? $this->route('treatment')
            ?? $this->route('flock')
            ?? $this->route('stock_movement')
            ?? $this->route('feed_production')
            ?? null;

        if ($entity === null) {
            return false;
        }

        return $this->user()?->can('approve', $entity) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
