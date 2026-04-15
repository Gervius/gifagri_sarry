<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecipePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view recipes')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les recettes.');
    }

    public function view(User $user, Recipe $recipe): Response
    {
        return $user->hasPermissionTo('view recipes')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cette recette.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create recipes')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des recettes.');
    }

    public function update(User $user, Recipe $recipe): Response
    {
        return $user->hasPermissionTo('update recipes')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les recettes.');
    }

    public function delete(User $user, Recipe $recipe): Response
    {
        return $user->hasPermissionTo('delete recipes')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les recettes.');
    }
}