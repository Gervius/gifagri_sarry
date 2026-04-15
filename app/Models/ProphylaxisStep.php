<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $prophylaxis_plan_id
 * @property int $day_of_age
 * @property string $treatment_type
 * @property string $administration_method
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read ProphylaxisPlan $prophylaxisPlan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ScheduledTreatment> $scheduledTreatments
 */
class ProphylaxisStep extends Model
{
    use HasFactory;

    protected $table = 'prophylaxis_steps';

    protected $guarded = ['id'];

    protected $casts = [
        'day_of_age' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function prophylaxisPlan(): BelongsTo
    {
        return $this->belongsTo(ProphylaxisPlan::class);
    }

    public function scheduledTreatments(): HasMany
    {
        return $this->hasMany(ScheduledTreatment::class);
    }
}