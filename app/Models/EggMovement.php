<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $date
 * @property string $type
 * @property int $quantity
 * @property float|null $unit_cost
 * @property string|null $source_type
 * @property int|null $source_id
 * @property string|null $notes
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model|null $source
 * @property-read User $creator
 */
class EggMovement extends Model
{
    use HasFactory;

    protected $table = 'egg_movements';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}