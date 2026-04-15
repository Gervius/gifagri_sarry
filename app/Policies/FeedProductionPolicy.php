<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FeedProduction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FeedProductionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view feed_productions')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les productions d\'aliment.');
    }

    public function view(User $user, FeedProduction $feedProduction): Response
    {
        return $user->hasPermissionTo('view feed_productions')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cette production d\'aliment.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create feed_productions')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des productions d\'aliment.');
    }

    public function update(User $user, FeedProduction $feedProduction): Response
    {
        if (!$feedProduction->isDraft()) {
            return Response::deny('Seules les productions d\'aliment en brouillon peuvent être modifiées.');
        }

        if (!$user->hasPermissionTo('update feed_productions')) {
            return Response::deny('Vous n\'avez pas la permission de modifier les productions d\'aliment.');
        }

        if (!$user->hasRole('Admin') && $feedProduction->created_by !== $user->id) {
            return Response::deny('Vous ne pouvez modifier que vos propres productions d\'aliment en brouillon.');
        }

        return Response::allow();
    }

    public function delete(User $user, FeedProduction $feedProduction): Response
    {
        if (!$feedProduction->isDraft()) {
            return Response::deny('Seules les productions d\'aliment en brouillon peuvent être supprimées.');
        }

        if (!$user->hasPermissionTo('delete feed_productions')) {
            return Response::deny('Vous n\'avez pas la permission de supprimer les productions d\'aliment.');
        }

        if (!$user->hasRole('Admin') && $feedProduction->created_by !== $user->id) {
            return Response::deny('Vous ne pouvez supprimer que vos propres productions d\'aliment en brouillon.');
        }

        return Response::allow();
    }

    public function approve(User $user, FeedProduction $feedProduction): Response
    {
        if (!$feedProduction->isDraft()) {
            return Response::deny('Seules les productions d\'aliment en brouillon peuvent être approuvées.');
        }

        return $user->hasPermissionTo('approve feed_productions')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission d\'approuver les productions d\'aliment.');
    }
}