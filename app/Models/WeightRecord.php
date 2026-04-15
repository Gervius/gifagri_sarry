<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $flock_id
 * @property \Illuminate\Support\Carbon $date
 * @property float $average_weight
 * @property int|null $sample_size
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Flock $flock
 */
class WeightRecord extends Model
{
    use HasFactory;

    protected $table = 'weight_records';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'average_weight' => 'decimal:2',
        'sample_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }
}