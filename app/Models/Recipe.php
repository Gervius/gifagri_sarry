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
 * @property string|null $description
 * @property float $yield
 * @property int $unit_id
 * @property int|null $animal_type_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Unit $unit
 * @property-read AnimalType|null $animalType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RecipeIngredient> $recipeIngredients
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FeedProduction> $feedProductions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FeedStock> $feedStocks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DailyRecord> $dailyRecords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductionPhase> $productionPhases
 */
class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recipes';

    protected $guarded = ['id'];

    protected $casts = [
        'yield' => 'decimal:2',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function feedProductions(): HasMany
    {
        return $this->hasMany(FeedProduction::class);
    }

    public function feedStocks(): HasMany
    {
        return $this->hasMany(FeedStock::class);
    }

    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class, 'feed_type_id');
    }

    public function productionPhases(): HasMany
    {
        return $this->hasMany(ProductionPhase::class, 'default_recipe_id');
    }
}