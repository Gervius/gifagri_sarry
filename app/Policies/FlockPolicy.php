<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Flock;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FlockPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les lots.');
    }

    public function view(User $user, Flock $flock): Response
    {
        return $user->hasPermissionTo('view flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir ce lot.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer un lot.');
    }

    public function update(User $user, Flock $flock): Response
    {
        // Ne peut modifier que si brouillon ou en attente (et permission)
        if (!in_array($flock->status, ['draft', 'pending'])) {
            return Response::deny('Ce lot ne peut plus être modifié.');
        }

        return $user->hasPermissionTo('update flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier ce lot.');
    }

    public function delete(User $user, Flock $flock): Response
    {
        // Ne peut supprimer que si brouillon
        if ($flock->status !== 'draft') {
            return Response::deny('Seuls les lots en brouillon peuvent être supprimés.');
        }

        return $user->hasPermissionTo('delete flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer ce lot.');
    }

    public function submit(User $user, Flock $flock): Response
    {
        if ($flock->status !== 'draft') {
            return Response::deny('Ce lot ne peut pas être soumis.');
        }

        return $user->hasPermissionTo('submit flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de soumettre ce lot.');
    }

    public function approve(User $user, Flock $flock): Response
    {
        if (!in_array($flock->status, ['pending'])) {
            return Response::deny('Ce lot ne peut pas être approuvé.');
        }

        // Vérifier la règle métier (invoice_id pour non-legacy)
        if (!$flock->canTransitionToActive()) {
            return Response::deny('Impossible d\'approuver : facture manquante pour un lot non-legacy.');
        }

        return $user->hasPermissionTo('approve flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission d\'approuver ce lot.');
    }

    public function reject(User $user, Flock $flock): Response
    {
        if (!in_array($flock->status, ['pending'])) {
            return Response::deny('Ce lot ne peut pas être rejeté.');
        }

        return $user->hasPermissionTo('reject flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de rejeter ce lot.');
    }

    public function end(User $user, Flock $flock): Response
    {
        if ($flock->status !== 'active') {
            return Response::deny('Seuls les lots actifs peuvent être terminés.');
        }

        return $user->hasPermissionTo('end flocks')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de terminer ce lot.');
    }
}