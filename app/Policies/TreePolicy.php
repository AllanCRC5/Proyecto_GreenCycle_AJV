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
            : Response::denyAsNotFound();
    }//permite consultar el árbol si pertenece al usuario

    public function care(User $user, Tree $tree): Response
    {
        return (int) $user->id === (int) $tree->user_id
            ? Response::allow()
            : Response::denyAsNotFound();//hace que un árbol ajeno responda como si no existiera: 404
    }//permite cuidar al arbol si pertenece al usuario
}
