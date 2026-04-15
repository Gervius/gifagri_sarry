<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int|null $weight_min_grams
 * @property int|null $weight_max_grams
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EggCategory extends Model
{
    use HasFactory;

    protected $table = 'egg_categories';

    protected $guarded = ['id'];

    protected $casts = [
        'weight_min_grams' => 'integer',
        'weight_max_grams' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}