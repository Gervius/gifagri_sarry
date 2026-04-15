<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $flock_id
 * @property int $phase_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Flock $flock
 * @property-read ProductionPhase $phase
 */
class FlockPhaseHistory extends Model
{
    use HasFactory;

    protected $table = 'flock_phase_histories';

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProductionPhase::class, 'phase_id');
    }
}