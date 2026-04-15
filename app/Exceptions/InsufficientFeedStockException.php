<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InsufficientFeedStockException extends Exception
{
    public function __construct(
        public readonly int $batchId,
        public readonly float $required,
        public readonly float $available,
    ) {
        parent::__construct(
            "Stock insuffisant pour le lot d'aliment ID #{$this->batchId}. "
            . "Requis: {$this->required} kg, Disponible: {$this->available} kg"
        );
    }
}
