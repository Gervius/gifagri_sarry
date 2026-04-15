<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class AccountingRuleNotFoundException extends Exception
{
    public function __construct(public readonly string $eventType)
    {
        parent::__construct(
            "Aucune règle comptable active trouvée pour l'événement '{$this->eventType}'."
        );
    }
}
