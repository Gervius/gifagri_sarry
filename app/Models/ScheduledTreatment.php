<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $flock_id
 * @property int $prophylaxis_step_id
 * @property string $scheduled_date
 * @property int $alert_days_before
 * @property string $status
 * @property int|null $actual_treatment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Flock $flock
 * @property-read ProphylaxisStep $prophylaxisStep
 * @property-read Treatment|null $actualTreatment
 */
class ScheduledTreatment extends Model
{
    use HasFactory;

    protected $table = 'scheduled_treatments';

    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_date' => 'date',
        'alert_days_before' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }

    public function prophylaxisStep(): BelongsTo
    {
        return $this->belongsTo(ProphylaxisStep::class);
    }

    public function actualTreatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class, 'actual_treatment_id');
    }
}