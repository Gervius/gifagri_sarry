<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $purchase_date
 * @property float $purchase_cost
 * @property int $lifespan_months
 * @property float $salvage_value
 * @property int $asset_account_id
 * @property int $depreciation_account_id
 * @property int $expense_account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Account $assetAccount
 * @property-read Account $depreciationAccount
 * @property-read Account $expenseAccount
 */
class FixedAsset extends Model
{
    use HasFactory;

    protected $table = 'fixed_assets';

    protected $guarded = ['id'];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'lifespan_months' => 'integer',
        'salvage_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assetAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'asset_account_id');
    }

    public function depreciationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'depreciation_account_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }
}