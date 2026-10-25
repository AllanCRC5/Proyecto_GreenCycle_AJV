<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TreeStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TreeResource;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TreeCareController extends Controller
{
    public function care(Request $request, Tree $tree)
    {
        return DB::transaction(function () use ($request, $tree) {
            // Bloqueamos el árbol para evitar cuidados simultáneos.
            $tree = Tree::query()
                ->whereKey($tree->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Un usuario solo puede cuidar sus propios árboles.
            // Verificamos que el árbol pertenezca al usuario autenticado.
            $authorization = Gate::inspect('care', $tree);

            if ($authorization->denied()) {
            return response()->json([
                'message' => 'TREE_NOT_FOUND',
                'code' => 'TREE_NOT_FOUND',
            ], Response::HTTP_NOT_FOUND);
}

            // Aplicamos primero el deterioro que tenga pendiente.
            $tree->applyDecay();

            // Si el deterioro mató al árbol, no permitimos cuidarlo.
            if ($tree->status === TreeStatus::DEAD) {
                return response()->json([
                    'message' => 'El árbol ha muerto.',
                    'code' => 'TREE_DEAD',
                ], Response::HTTP_CONFLICT);
            }

            // Solo los árboles activos pueden recibir cuidados.
            if ($tree->status !== TreeStatus::ACTIVE) {
                return response()->json([
                    'message' => 'El árbol no está activo.',
                    'code' => 'TREE_NOT_ACTIVE',
                ], Response::HTTP_CONFLICT);
            }

            // Comprobamos el tiempo de espera entre cuidados.
            if ($tree->isInCooldown()) {
                return response()->json([
                    'message' => 'El árbol todavía está en cooldown.',
                    'code' => 'CARE_COOLDOWN',
                    'retry_at' => $tree->next_care_at,
                ], Response::HTTP_CONFLICT);
            }

            // Aplicamos el cuidado y guardamos el progreso.
            $tree->applyCare();

            return new TreeResource($tree->refresh());
        });
    }
}
