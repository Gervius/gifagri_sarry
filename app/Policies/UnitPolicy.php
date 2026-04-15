<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UnitPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view units')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les unités.');
    }

    public function view(User $user, Unit $unit): Response
    {
        return $user->hasPermissionTo('view units')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cette unité.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create units')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des unités.');
    }

    public function update(User $user, Unit $unit): Response
    {
        return $user->hasPermissionTo('update units')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les unités.');
    }

    public function delete(User $user, Unit $unit): Response
    {
        return $user->hasPermissionTo('delete units')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les unités.');
    }
}
