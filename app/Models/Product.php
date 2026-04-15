<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $stock_quantity
 * @property int $unit_id
 * @property int $accounting_account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Unit $unit
 * @property-read Account $accountingAccount
 */
class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $guarded = ['id'];

    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accounting_account_id');
    }
}