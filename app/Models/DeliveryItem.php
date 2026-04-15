<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $delivery_id
 * @property string $description
 * @property string $itemable_type
 * @property int $itemable_id
 * @property float $expected_quantity
 * @property float|null $delivered_quantity
 * @property float $unit_price
 * @property float $total
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Delivery $delivery
 * @property-read Model $itemable
 */
class DeliveryItem extends Model
{
    use HasFactory;

    protected $table = 'delivery_items';

    protected $guarded = ['id'];

    protected $casts = [
        'expected_quantity' => 'decimal:2',
        'delivered_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }
}
