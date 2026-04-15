<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
/**
 * @property int $id
 * @property string $number
 * @property string $type
 * @property int|null $partner_id
 * @property string|null $customer_name
 * @property string $date
 * @property string|null $due_date
 * @property float $subtotal
 * @property float $tax_rate
 * @property float $tax_amount
 * @property float $total
 * @property string $status
 * @property string $payment_status
 * @property string|null $notes
 * @property int $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Partner|null $partner
 * @property-read User $creator
 * @property-read User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InvoiceItem> $items
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Tax> $taxes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Flock> $flocks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Receipt> $receipts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Delivery> $deliveries
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'invoices';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function taxes(): HasMany
    {
        return $this->belongsToMany(Tax::class, 'invoice_tax')
                    ->withPivot('calculated_amount')
                    ->withTimestamps();
    }

    public function flocks(): HasMany
    {
        return $this->hasMany(Flock::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['number', 'type', 'partner_id', 'customer_name', 'date', 'due_date', 'subtotal', 'tax_rate', 'tax_amount', 'total', 'status', 'payment_status', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}