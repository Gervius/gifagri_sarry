<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AccountingRule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AccountingRulePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view accounting_rules')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les règles comptables.');
    }

    public function view(User $user, AccountingRule $accountingRule): Response
    {
        return $user->hasPermissionTo('view accounting_rules')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cette règle comptable.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create accounting_rules')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des règles comptables.');
    }

    public function update(User $user, AccountingRule $accountingRule): Response
    {
        return $user->hasPermissionTo('update accounting_rules')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les règles comptables.');
    }

    public function delete(User $user, AccountingRule $accountingRule): Response
    {
        return $user->hasPermissionTo('delete accounting_rules')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les règles comptables.');
    }
}