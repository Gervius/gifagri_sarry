<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $flock_id
 * @property string $event_type
 * @property \Illuminate\Support\Carbon $event_date
 * @property int|null $piglets_born_alive
 * @property int|null $piglets_stillborn
 * @property int|null $piglets_weaned
 * @property int|null $boar_flock_id
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Flock $flock
 * @property-read Flock|null $boarFlock
 */
class PigBreedingEvent extends Model
{
    use HasFactory;

    protected $table = 'pig_breeding_events';

    protected $guarded = ['id'];

    protected $casts = [
        'event_date' => 'date',
        'piglets_born_alive' => 'integer',
        'piglets_stillborn' => 'integer',
        'piglets_weaned' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }

    public function boarFlock(): BelongsTo
    {
        return $this->belongsTo(Flock::class, 'boar_flock_id');
    }
}