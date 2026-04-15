<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalEntry> $journalEntries
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BankAccount> $bankAccounts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tax> $taxes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FixedAsset> $fixedAssets
 */
class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'accounting_account_id');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class, 'accounting_account_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'accounting_account_id');
    }

    public function fixedAssetsAsAsset(): HasMany
    {
        return $this->hasMany(FixedAsset::class, 'asset_account_id');
    }

    public function fixedAssetsAsDepreciation(): HasMany
    {
        return $this->hasMany(FixedAsset::class, 'depreciation_account_id');
    }

    public function fixedAssetsAsExpense(): HasMany
    {
        return $this->hasMany(FixedAsset::class, 'expense_account_id');
    }
}