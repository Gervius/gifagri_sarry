<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $invoices
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Ingredient> $ingredients
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Flock> $flocks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PurchasePrice> $purchasePrices
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Receipt> $receipts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Delivery> $deliveries
 */
class Partner extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'partners';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function flocks(): HasMany
    {
        return $this->hasMany(Flock::class, 'supplier_id');
    }

    public function purchasePrices(): HasMany
    {
        return $this->hasMany(PurchasePrice::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }
}