<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProductionPhase;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductionPhasePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view production_phases')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les phases de production.');
    }

    public function view(User $user, ProductionPhase $productionPhase): Response
    {
        return $user->hasPermissionTo('view production_phases')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cette phase de production.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create production_phases')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des phases de production.');
    }

    public function update(User $user, ProductionPhase $productionPhase): Response
    {
        return $user->hasPermissionTo('update production_phases')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les phases de production.');
    }

    public function delete(User $user, ProductionPhase $productionPhase): Response
    {
        return $user->hasPermissionTo('delete production_phases')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les phases de production.');
    }
}
