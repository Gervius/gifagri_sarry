<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property bool $can_lay_eggs
 * @property bool $has_growth_tracking
 * @property bool $has_breeding_cycle
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Flock> $flocks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductionPhase> $productionPhases
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Recipe> $recipes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProphylaxisPlan> $prophylaxisPlans
 */
class AnimalType extends Model
{
    use HasFactory;

    protected $table = 'animal_types';

    protected $guarded = ['id'];

    protected $casts = [
        'can_lay_eggs' => 'boolean',
        'has_growth_tracking' => 'boolean',
        'has_breeding_cycle' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function flocks(): HasMany
    {
        return $this->hasMany(Flock::class);
    }

    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    public function productionPhases(): HasMany
    {
        return $this->hasMany(ProductionPhase::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function prophylaxisPlans(): HasMany
    {
        return $this->hasMany(ProphylaxisPlan::class);
    }
}