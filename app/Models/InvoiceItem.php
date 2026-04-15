<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $invoice_id
 * @property string $description
 * @property string|null $itemable_type
 * @property int|null $itemable_id
 * @property float $quantity
 * @property float $unit_price
 * @property float $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Invoice $invoice
 * @property-read Model|null $itemable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LandedCostAllocation> $landedCostAllocations
 */
class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_items';

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    public function landedCostAllocations(): HasMany
    {
        return $this->hasMany(LandedCostAllocation::class, 'invoice_item_id');
    }
}