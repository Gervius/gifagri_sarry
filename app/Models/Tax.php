<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property float $rate
 * @property string $type
 * @property int $accounting_account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Account $accountingAccount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $invoices
 */
class Tax extends Model
{
    use HasFactory;

    protected $table = 'taxes';

    protected $guarded = ['id'];

    protected $casts = [
        'rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function accountingAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'accounting_account_id');
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_tax')
                    ->withPivot('calculated_amount')
                    ->withTimestamps();
    }
}