<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property int $recipe_id
 * @property float $quantity_produced
 * @property int $unit_id
 * @property string $production_date
 * @property string|null $notes
 * @property string $status
 * @property int $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Recipe $recipe
 * @property-read Unit $unit
 * @property-read User $creator
 * @property-read User|null $approver
 */
class FeedProduction extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'feed_productions';

    protected $guarded = ['id'];

    protected $casts = [
        'quantity_produced' => 'decimal:2',
        'production_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['recipe_id', 'quantity_produced', 'unit_id', 'production_date', 'notes', 'status', 'approved_by', 'approved_at', 'rejection_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}