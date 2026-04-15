<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PigBreedingEvent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PigBreedingEventPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view pig_breeding_events')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les événements de reproduction.');
    }

    public function view(User $user, PigBreedingEvent $event): Response
    {
        return $user->hasPermissionTo('view pig_breeding_events')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cet événement de reproduction.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create pig_breeding_events')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des événements de reproduction.');
    }

    public function update(User $user, PigBreedingEvent $event): Response
    {
        return $user->hasPermissionTo('update pig_breeding_events')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les événements de reproduction.');
    }

    public function delete(User $user, PigBreedingEvent $event): Response
    {
        return $user->hasPermissionTo('delete pig_breeding_events')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les événements de reproduction.');
    }
}
