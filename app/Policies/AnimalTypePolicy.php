<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AnimalType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnimalTypePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view animal_types')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir le catalogue.');
    }

    public function view(User $user, AnimalType $animalType): Response
    {
        return $user->hasPermissionTo('view animal_types')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir ce type d\'animal.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create animal_types')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer un type d\'animal.');
    }

    public function update(User $user, AnimalType $animalType): Response
    {
        return $user->hasPermissionTo('update animal_types')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier un type d\'animal.');
    }

    public function delete(User $user, AnimalType $animalType): Response
    {
        return $user->hasPermissionTo('delete animal_types')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer un type d\'animal.');
    }
}