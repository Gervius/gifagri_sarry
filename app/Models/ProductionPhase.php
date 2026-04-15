<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $animal_type_id
 * @property string $name
 * @property int|null $typical_duration_days
 * @property int $order
 * @property int|null $default_recipe_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read AnimalType $animalType
 * @property-read Recipe|null $defaultRecipe
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FlockPhaseHistory> $flockPhaseHistories
 */
class ProductionPhase extends Model
{
    use HasFactory;

    protected $table = 'production_phases';

    protected $guarded = ['id'];

    protected $casts = [
        'typical_duration_days' => 'integer',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function defaultRecipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class, 'default_recipe_id');
    }

    public function flockPhaseHistories(): HasMany
    {
        return $this->hasMany(FlockPhaseHistory::class);
    }

    public function allowsEggCollection(): bool
    {
        // Pour les layers, permettre la collecte si la phase est de production (order >= 2 par exemple)
        if ($this->animalType->code === 'layer') {
            return $this->order >= 2; // Assumer que phase 1 est démarrage, 2+ production
        }
        return false;
    }
}