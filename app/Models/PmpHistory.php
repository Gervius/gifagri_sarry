<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $ingredient_id
 * @property string $date
 * @property float $old_pmp
 * @property float $new_pmp
 * @property string $source_type
 * @property int $source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Ingredient $ingredient
 * @property-read Model $source
 */
class PmpHistory extends Model
{
    use HasFactory;

    protected $table = 'pmp_histories';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'old_pmp' => 'decimal:2',
        'new_pmp' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}