<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $stockable_type
 * @property int $stockable_id
 * @property float $expected_quantity
 * @property float $actual_quantity
 * @property string|null $reason
 * @property int $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $stockable
 * @property-read User $creator
 * @property-read User|null $approver
 */
class InventoryAdjustment extends Model
{
    use HasFactory;

    protected $table = 'inventory_adjustments';

    protected $guarded = ['id'];

    protected $casts = [
        'expected_quantity' => 'decimal:2',
        'actual_quantity' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}