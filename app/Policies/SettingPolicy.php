<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SettingPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('view settings')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir les paramètres.');
    }

    public function view(User $user, Setting $setting): Response
    {
        return $user->hasPermissionTo('view settings')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de voir ce paramètre.');
    }

    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create settings')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de créer des paramètres.');
    }

    public function update(User $user, Setting $setting): Response
    {
        return $user->hasPermissionTo('update settings')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de modifier les paramètres.');
    }

    public function delete(User $user, Setting $setting): Response
    {
        return $user->hasPermissionTo('delete settings')
            ? Response::allow()
            : Response::deny('Vous n\'avez pas la permission de supprimer les paramètres.');
    }
}
