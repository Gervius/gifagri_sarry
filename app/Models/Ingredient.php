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
 * @property string|null $reference
 * @property int $default_unit_id
 * @property float $current_stock
 * @property float $current_stock_base
 * @property float|null $min_stock
 * @property float|null $max_stock
 * @property float $pmp
 * @property string|null $description
 * @property int|null $partner_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Unit $defaultUnit
 * @property-read Partner|null $partner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RecipeIngredient> $recipeIngredients
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockMovement> $stockMovements
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Batch> $batches
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PmpHistory> $pmpHistories
 */
class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ingredients';

    protected $guarded = ['id'];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'current_stock_base' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'pmp' => 'decimal:2',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function defaultUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'batchable_id')->where('batchable_type', Ingredient::class);
    }

    public function pmpHistories(): HasMany
    {
        return $this->hasMany(PmpHistory::class);
    }
}