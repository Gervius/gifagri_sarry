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
 * @property string $symbol
 * @property string $type
 * @property float|null $conversion_factor
 * @property int|null $base_unit_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Unit|null $baseUnit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Unit> $subUnits
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Ingredient> $ingredients
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Recipe> $recipes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FeedProduction> $feedProductions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockMovement> $stockMovements
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FeedStock> $feedStocks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 */
class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $guarded = ['id'];

    protected $casts = [
        'conversion_factor' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function subUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'default_unit_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'unit_id');
    }

    public function feedProductions(): HasMany
    {
        return $this->hasMany(FeedProduction::class, 'unit_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'unit_id');
    }

    public function feedStocks(): HasMany
    {
        return $this->hasMany(FeedStock::class, 'unit_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}