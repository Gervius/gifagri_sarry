<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BatchPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view batches')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les lots.');
    }

    public function view(User $user, Batch $batch): Response
    {
        return $user->hasPermissionTo('view batches')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir ce lot.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create batches')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des lots.');
    }

    public function update(User $user, Batch $batch): Response
    {
        return $user->hasPermissionTo('update batches')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les lots.');
    }

    public function delete(User $user, Batch $batch): Response
    {
        return $user->hasPermissionTo('delete batches')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les lots.');
    }
}
