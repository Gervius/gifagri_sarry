<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $target_type
 * @property int $target_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $target
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AnalyticalAllocation> $analyticalAllocations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Flock> $flocks
 */
class AnalyticalAccount extends Model
{
    use HasFactory;

    protected $table = 'analytical_accounts';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function analyticalAllocations(): HasMany
    {
        return $this->hasMany(AnalyticalAllocation::class);
    }

    public function flocks(): HasMany
    {
        return $this->hasMany(Flock::class);
    }
}