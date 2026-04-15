<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $animal_type_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read AnimalType $animalType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProphylaxisStep> $steps
 */
class ProphylaxisPlan extends Model
{
    use HasFactory;

    protected $table = 'prophylaxis_plans';

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ProphylaxisStep::class);
    }
}