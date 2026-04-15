<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property bool $is_closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class FiscalYear extends Model
{
    use HasFactory;

    protected $table = 'fiscal_years';

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}