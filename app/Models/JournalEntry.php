<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $journal_voucher_id
 * @property int $account_id
 * @property float $debit
 * @property float $credit
 * @property string|null $description
 *
 * @property-read JournalVoucher $journalVoucher
 * @property-read Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AnalyticalAllocation> $analyticalAllocations
 */
class JournalEntry extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'journal_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journalVoucher(): BelongsTo
    {
        return $this->belongsTo(JournalVoucher::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function analyticalAllocations(): HasMany
    {
        return $this->hasMany(AnalyticalAllocation::class);
    }
}