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

class TreeController extends Controller
{
    public function index(Request $request)
    {
        $trees = $request->user()
            ->tree()
            ->latest()
            ->paginate(15);

        return TreeResource::collection($trees);
    }

    public function store(TreeRequest $request)
    {
        $validated = $request->validated();

        $tree = DB::transaction(function () use ($request, $validated) {
            $authenticatedUser = $request->user();

            // Evita que dos solicitudes simultáneas superen el límite.
            $user = User::query()
                ->whereKey($authenticatedUser->id)
                ->lockForUpdate()
                ->firstOrFail();

            $activeTrees = $user->tree()
                ->where('status', TreeStatus::ACTIVE->value)
                ->count();

            $maxActiveTrees = (int) config(
                'greencycle.max_active_trees',
                5
            );

            if ($activeTrees >= $maxActiveTrees) {
                abort(
                    Response::HTTP_CONFLICT,
                    'TREE_LIMIT_REACHED'
                );
            }

            $tree = new Tree();

            $tree->seed_type = $validated['seed_type']
                ?? SeedType::BASIC->value;

            // Los valores iniciales los establece el servidor.
            
            $tree->level = 0;
            $tree->health = config('greencycle.health_initial', 100);
            $tree->progress = config('greencycle.progress_initial', 0);
            $tree->status = TreeStatus::ACTIVE;
            $tree->last_decay_at = now();


            $tree->user()->associate($user);
            $tree->save();

            return $tree;
        });

        return (new TreeResource($tree))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, Tree $tree)
    {
        $this->ensureUserOwnsTree($request, $tree);

        return new TreeResource($tree);
    }

    private function ensureUserOwnsTree(
        Request $request,
        Tree $tree
    ): void {
        abort_unless(
            (int) $tree->user_id === (int) $request->user()->id,
            Response::HTTP_NOT_FOUND
        );
    }
}

