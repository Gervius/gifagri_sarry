<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $recipe_id
 * @property int $ingredient_id
 * @property float $quantity
 * @property int $unit_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Recipe $recipe
 * @property-read Ingredient $ingredient
 * @property-read Unit $unit
 */
class RecipeIngredient extends Model
{
    use HasFactory;

    protected $table = 'recipe_ingredients';

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}