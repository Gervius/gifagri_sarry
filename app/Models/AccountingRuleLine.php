<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $accounting_rule_id
 * @property string $type
 * @property string $account_resolution_type
 * @property int|null $account_id
 * @property string|null $dynamic_account_placeholder
 * @property string $amount_source
 * @property float $percentage
 * @property string|null $description_template
 * @property string|null $analytical_target_source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read AccountingRule $accountingRule
 * @property-read Account|null $account
 */
class AccountingRuleLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_rule_lines';

    protected $guarded = ['id'];

    protected $casts = [
        'percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function accountingRule(): BelongsTo
    {
        return $this->belongsTo(AccountingRule::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}