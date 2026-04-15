<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EggCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EggCategoryPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view egg_categories')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les catégories d\'œufs.');
    }

    public function view(User $user, EggCategory $eggCategory): Response
    {
        return $user->hasPermissionTo('view egg_categories')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir cette catégorie d\'œuf.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create egg_categories')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des catégories d\'œufs.');
    }

    public function update(User $user, EggCategory $eggCategory): Response
    {
        return $user->hasPermissionTo('update egg_categories')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les catégories d\'œufs.');
    }

    public function delete(User $user, EggCategory $eggCategory): Response
    {
        return $user->hasPermissionTo('delete egg_categories')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les catégories d\'œufs.');
    }
}
