<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Treatment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TreatmentPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view treatments')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les traitements.');
    }

    public function view(User $user, Treatment $treatment): Response
    {
        return $user->hasPermissionTo('view treatments')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir ce traitement.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create treatments')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des traitements.');
    }

    public function update(User $user, Treatment $treatment): Response
    {
        if (!$treatment->isDraft()) {
            return Response::deny('Seuls les traitements en brouillon peuvent être modifiés.');
        }

        if (!$user->hasPermissionTo('update treatments')) {
            return Response::deny('Vous n\'avez pas la permission de modifier les traitements.');
        }

        if (!$user->hasRole('Admin') && $treatment->created_by !== $user->id) {
            return Response::deny('Vous ne pouvez modifier que vos propres traitements en brouillon.');
        }

        return Response::allow();
    }

    public function delete(User $user, Treatment $treatment): Response
    {
        if (!$treatment->isDraft()) {
            return Response::deny('Seuls les traitements en brouillon peuvent être supprimés.');
        }

        if (!$user->hasPermissionTo('delete treatments')) {
            return Response::deny('Vous n\'avez pas la permission de supprimer les traitements.');
        }

        if (!$user->hasRole('Admin') && $treatment->created_by !== $user->id) {
            return Response::deny('Vous ne pouvez supprimer que vos propres traitements en brouillon.');
        }

        return Response::allow();
    }

    public function approve(User $user, Treatment $treatment): Response
    {
        if (! $treatment->isDraft()) {
            return Response::deny('Seuls les traitements en brouillon peuvent être approuvés.');
        }

        if (! $user->hasRole(['Manager', 'Admin'])) {
            return Response::deny('Seuls les managers et administrateurs peuvent approuver les traitements.');
        }

        return Response::allow();
    }

    public function reject(User $user, Treatment $treatment): Response
    {
        if (! $treatment->isDraft()) {
            return Response::deny('Seuls les traitements en brouillon peuvent être rejetés.');
        }

        if (! $user->hasRole(['Manager', 'Admin'])) {
            return Response::deny('Seuls les managers et administrateurs peuvent rejeter les traitements.');
        }

        return Response::allow();
    }
}
