<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string|null $voucher_number
 * @property string $date
 * @property string|null $description
 * @property string $source_type
 * @property int $source_id
 * @property string $status
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Model $source
 * @property-read User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalEntry> $journalEntries
 */
class JournalVoucher extends Model
{
    use HasFactory;

    protected $table = 'journal_vouchers';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}