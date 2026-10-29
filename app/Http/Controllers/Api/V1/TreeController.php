<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SeedType;
use App\Enums\TreeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TreeRequest;
use App\Http\Resources\TreeResource;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TreeController extends Controller
{
    /*
    index() lista los árboles del usuario autenticado y pagina los resultados.
    store() utiliza una transacción y lockForUpdate() para proteger el límite de árboles activos.
    El límite se obtiene de config/greencycle.php.
    Los valores iniciales del árbol los establece el servidor.
    La creación devuelve HTTP 201 Created.
    show() impide consultar árboles de otros usuarios y responde con 404.
    */
    public function index(Request $request)
    {
        $trees = $request->user()
            ->trees()
            ->latest()
            ->paginate(15);

        return TreeResource::collection($trees);
    }

    public function store(TreeRequest $request)
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($request, $validated) {
            $authenticatedUser = $request->user();

            $user = User::query()
                ->whereKey($authenticatedUser->id)
                ->lockForUpdate()
                ->firstOrFail();

            $activeTrees = $user->trees()
                ->where('status', TreeStatus::ACTIVE->value)
                ->count();

            $maxActiveTrees = (int) config(
                'greencycle.max_active_trees',
                5
            );

            if ($activeTrees >= $maxActiveTrees) {
                return null;
            }

            $tree = new Tree;

            $tree->seed_type = $validated['seed_type']
                ?? SeedType::BASIC->value;

            $tree->level = 0;
            $tree->health = config('greencycle.health_initial', 100);
            $tree->progress = config('greencycle.progress_initial', 0);
            $tree->status = TreeStatus::ACTIVE;
            $tree->last_decay_at = now();

            $tree->user()->associate($user);
            $tree->save();

            return $tree;
        });

        if ($result === null) {
            return response()->json([
                'code' => 'TREE_LIMIT_REACHED',
                'message' => 'Has alcanzado el límite de árboles activos.',
            ], 409);
        }

        return (new TreeResource($result))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Tree $tree)
    {
        Gate::authorize('view', $tree);

        return new TreeResource($tree);
    }
}
