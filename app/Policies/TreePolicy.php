<?php

namespace App\Policies;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TreePolicy
{
    public function view(User $user, Tree $tree): Response
    {
        return (int) $user->id === (int) $tree->user_id
            ? Response::allow()
            : Response::denyAsNotFound(
                'El recurso solicitado no existe.'
            );
    }

    public function care(User $user, Tree $tree): Response
    {
        return (int) $user->id === (int) $tree->user_id
            ? Response::allow()
            : Response::denyAsNotFound(
                'El recurso solicitado no existe.'
            );
    }
}
