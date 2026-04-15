<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property int $animal_type_id
 * @property int $building_id
 * @property \Illuminate\Support\Carbon $arrival_date
 * @property int $initial_quantity
 * @property int|null $current_quantity
 * @property float|null $purchase_cost
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property string|null $end_reason
 * @property float|null $standard_mortality_rate
 * @property int|null $supplier_id
 * @property int|null $invoice_id
 * @property int|null $analytical_account_id
 * @property string|null $notes
 * @property int $created_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property bool $is_legacy
 *
 * @property-read AnimalType $animalType
 * @property-read Building $building
 * @property-read Partner|null $supplier
 * @property-read Invoice|null $invoice
 * @property-read AnalyticalAccount|null $analyticalAccount
 * @property-read User $creator
 * @property-read User|null $approver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, DailyRecord> $dailyRecords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Treatment> $treatments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, WeightRecord> $weightRecords
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PigBreedingEvent> $pigBreedingEvents
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FlockPhaseHistory> $phaseHistories
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ScheduledTreatment> $scheduledTreatments
 */
class Flock extends Model
{
    use HasFactory, SoftDeletes, LogsActivity; 

    protected $guarded = ['id'];

    protected $casts = [
        'arrival_date' => 'date',
        'ended_at' => 'datetime',
        'standard_mortality_rate' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_legacy' => 'boolean',
    ];

    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'supplier_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function analyticalAccount(): BelongsTo
    {
        return $this->belongsTo(AnalyticalAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dailyRecords(): HasMany
    {
        return $this->hasMany(DailyRecord::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    public function weightRecords(): HasMany
    {
        return $this->hasMany(WeightRecord::class);
    }

    public function pigBreedingEvents(): HasMany
    {
        return $this->hasMany(PigBreedingEvent::class);
    }

    public function phaseHistories(): HasMany
    {
        return $this->hasMany(FlockPhaseHistory::class);
    }

    public function getCurrentPhase(): ?\App\Models\ProductionPhase
    {
        $currentHistory = $this->phaseHistories()->whereNull('end_date')->first();
        return $currentHistory?->phase;
    }

    public function getState(): \App\States\Flock\FlockState
    {
        return \App\States\Flock\FlockStateFactory::create($this->status);
    }

    public function getSpeciesBehavior(): \App\Strategies\Species\SpeciesBehaviorStrategy
    {
        return \App\Strategies\Species\SpeciesBehaviorFactory::create($this);
    }

    public function canTransitionToActive(): bool
    {
        return $this->getState()->canTransitionToActive($this);
    }

    public function transitionToActive(): void
    {
        $this->getState()->transitionToActive($this);
    }

    public function canSubmitDailyRecord(): bool
    {
        return $this->getState()->canSubmitDailyRecord($this);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function invoiceItems(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\InvoiceItem::class, 'itemable');
    }
    

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'animal_type_id', 'building_id', 'arrival_date', 'initial_quantity', 'current_quantity', 'purchase_cost', 'status', 'ended_at', 'end_reason', 'standard_mortality_rate', 'supplier_id', 'invoice_id', 'analytical_account_id', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}