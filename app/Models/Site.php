<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = ['name', 'short_name'];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }
}
