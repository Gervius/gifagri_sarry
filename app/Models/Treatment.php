<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $flock_id
 * @property string $treatment_date
 * @property string|null $veterinarian
 * @property string $treatment_type
 * @property string|null $description
 * @property float|null $cost
 * @property string|null $invoice_reference
 * @property int|null $batch_id
 * @property string $status
 * @property int $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Flock $flock
 * @property-read Batch|null $batch
 * @property-read User $creator
 * @property-read User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ScheduledTreatment> $scheduledTreatments
 */
class Treatment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'treatments';

    protected $guarded = ['id'];

    protected $casts = [
        'treatment_date' => 'date',
        'cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scheduledTreatments(): HasMany
    {
        return $this->hasMany(ScheduledTreatment::class, 'actual_treatment_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}