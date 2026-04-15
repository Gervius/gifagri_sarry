<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Treatment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TreatmentApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Treatment $treatment,
    ) {
    }
}
