<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\DailyRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DailyRecordApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly DailyRecord $dailyRecord,
    ) {
    }
}
