<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $flock_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $losses
 * @property int $eggs
 * @property float $feed_consumed
 * @property int|null $feed_type_id
 * @property int|null $feed_batch_id
 * @property float $water_consumed
 * @property string|null $notes
 * @property string $status
 * @property int $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Flock $flock
 * @property-read Recipe|null $feedType
 * @property-read Batch|null $feedBatch
 * @property-read User $creator
 * @property-read User|null $approver
 */
class DailyRecord extends Model
{
    use HasFactory;

    protected $table = 'daily_records';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'losses' => 'integer',
        'eggs' => 'integer',
        'feed_consumed' => 'decimal:2',
        'water_consumed' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flock(): BelongsTo
    {
        return $this->belongsTo(Flock::class);
    }

    public function feedType(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'feed_type_id');
    }

    public function feedBatch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'feed_batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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