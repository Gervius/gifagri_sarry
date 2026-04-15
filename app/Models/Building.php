<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $capacity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Flock> $flocks
 */
class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';

    protected $guarded = ['id'];

    protected $casts = [
        'capacity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function flocks(): HasMany
    {
        return $this->hasMany(Flock::class);
    }
}
