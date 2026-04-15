<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StockMovementPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view stock_movements')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les mouvements de stock.');
    }

    public function view(User $user, StockMovement $stockMovement): Response
    {
        return $user->hasPermissionTo('view stock_movements')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir ce mouvement de stock.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create stock_movements')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des mouvements de stock.');
    }

    public function update(User $user, StockMovement $stockMovement): Response
    {
        if (!$stockMovement->isPending()) {
            return Response::deny('Seuls les mouvements de stock en attente peuvent être modifiés.');
        }

        if (!$user->hasPermissionTo('update stock_movements')) {
            return Response::deny('Vous n\'avez pas la permission de modifier les mouvements de stock.');
        }

        if (!$user->hasRole('Admin') && $stockMovement->created_by !== $user->id) {
            return Response::deny('Vous ne pouvez modifier que vos propres mouvements de stock en attente.');
        }

        return Response::allow();
    }

    public function delete(User $user, StockMovement $stockMovement): Response
    {
        if (!$stockMovement->isPending()) {
            return Response::deny('Seuls les mouvements de stock en attente peuvent être supprimés.');
        }

        if (!$user->hasPermissionTo('delete stock_movements')) {
            return Response::deny('Vous n\'avez pas la permission de supprimer les mouvements de stock.');
        }

        if (!$user->hasRole('Admin') && $stockMovement->created_by !== $user->id) {
            return Response::deny('Vous ne pouvez supprimer que vos propres mouvements de stock en attente.');
        }

        return Response::allow();
    }

    public function approve(User $user, StockMovement $stockMovement): Response
    {
        if (!$user->hasPermissionTo('approve stock_movements')) {
            return Response::deny('Vous n\'avez pas la permission d\'approuver les mouvements de stock.');
        }

        if (!$user->hasRole('Admin')) {
            if (!$user->hasRole('Manager') || $stockMovement->created_by === $user->id) {
                return Response::deny('Seuls les administrateurs ou les managers différents de l\'auteur peuvent approuver les mouvements de stock.');
            }
        }

        return Response::allow();
    }
}