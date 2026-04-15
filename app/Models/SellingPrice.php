<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $sellable_type
 * @property int $sellable_id
 * @property float $price
 * @property string $effective_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $sellable
 */
class SellingPrice extends Model
{
    use HasFactory;

    protected $table = 'selling_prices';

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'effective_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sellable(): MorphTo
    {
        return $this->morphTo();
    }
}