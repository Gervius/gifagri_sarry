<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class AccountingImbalanceException extends Exception
{
    public function __construct(
        public readonly int $journalVoucherId,
        public readonly float $totalDebit,
        public readonly float $totalCredit,
    ) {
        parent::__construct(
            "Déséquilibre comptable pour la pièce #{$this->journalVoucherId}. "
            . "Total débit: {$this->totalDebit}, Total crédit: {$this->totalCredit}."
        );
    }
}
