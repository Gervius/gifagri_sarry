<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $batch_number
 * @property string|null $manufacturing_date
 * @property string|null $expiration_date
 * @property float $initial_quantity
 * @property float $current_quantity
 * @property string $batchable_type
 * @property int $batchable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $batchable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockMovement> $stockMovements
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DailyRecord> $dailyRecords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Treatment> $treatments
 */
class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';

    protected $guarded = ['id'];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiration_date' => 'date',
        'initial_quantity' => 'decimal:2',
        'current_quantity' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function batchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class, 'feed_batch_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class, 'batch_id');
    }
}