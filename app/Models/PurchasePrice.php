<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $purchasable_type
 * @property int $purchasable_id
 * @property int|null $partner_id
 * @property float $price
 * @property string $effective_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $purchasable
 * @property-read Partner|null $partner
 */
class PurchasePrice extends Model
{
    use HasFactory;

    protected $table = 'purchase_prices';

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'effective_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}