<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property int $invoice_id
 * @property float $amount
 * @property string $payment_date
 * @property string|null $method
 * @property int|null $bank_account_id
 * @property string|null $reference
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Invoice $invoice
 * @property-read BankAccount|null $bankAccount
 * @property-read User $creator
 */
class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'payments';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['invoice_id', 'amount', 'payment_date', 'method', 'bank_account_id', 'reference'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}