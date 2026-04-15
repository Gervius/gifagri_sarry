<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $stockable_type
 * @property int $stockable_id
 * @property float $quantity
 * @property string $type
 * @property string $reason
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $stockable
 */
class StockMovement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    /**
     * Relation polymorphique vers l'élément stockable.
     */
    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }
}