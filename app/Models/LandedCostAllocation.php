<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $invoice_item_id
 * @property string $target_item_type
 * @property int $target_item_id
 * @property float $allocated_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read InvoiceItem $invoiceItem
 * @property-read Model $targetItem
 */
class LandedCostAllocation extends Model
{
    use HasFactory;

    protected $table = 'landed_cost_allocations';

    protected $guarded = ['id'];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function targetItem(): MorphTo
    {
        return $this->morphTo();
    }
}