<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $account_number
 * @property int $accounting_account_id
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Account $accountingAccount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 */
class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accounting_account_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}