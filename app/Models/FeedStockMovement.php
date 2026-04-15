<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $feed_stock_id
 * @property string $type
 * @property float $quantity
 * @property float|null $unit_price
 * @property string|null $source_type
 * @property int|null $source_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read FeedStock $feedStock
 * @property-read Model|null $source
 * @property-read User $creator
 */
class FeedStockMovement extends Model
{
    use HasFactory;

    protected $table = 'feed_stock_movements';

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function feedStock(): BelongsTo
    {
        return $this->belongsTo(FeedStock::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}