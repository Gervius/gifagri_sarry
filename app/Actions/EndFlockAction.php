<?php

namespace App\Actions;

use App\Models\Flock;
use App\Models\User;

class EndFlockAction
{
    public function execute(Flock $flock, array $data, User $user): void
    {
        $flock->status = 'completed';
        $flock->ended_at = now();
        $flock->end_reason = $data['end_reason'];
        $flock->notes = $data['notes'] ?? null;

        if ($data['end_reason'] === 'sale') {
            
            $flock->end_sale_date = $data['sale_date'];
            $flock->end_sale_price = $data['sale_price'];
            $flock->end_sale_customer = $data['sale_customer'];
            $flock->end_sale_invoice_ref = $data['sale_invoice_ref'];
        }

        $flock->save();

    }
}