<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $journal_entry_id
 * @property int $analytical_account_id
 * @property float|null $percentage
 * @property float|null $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read JournalEntry $journalEntry
 * @property-read AnalyticalAccount $analyticalAccount
 */
class AnalyticalAllocation extends Model
{
    use HasFactory;

    protected $table = 'analytical_allocations';

    protected $guarded = ['id'];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function analyticalAccount(): BelongsTo
    {
        return $this->belongsTo(AnalyticalAccount::class);
    }
}