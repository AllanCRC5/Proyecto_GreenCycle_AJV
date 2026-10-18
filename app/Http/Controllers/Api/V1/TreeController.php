<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // éxito:201 y error:422
        $tree = $request->user()->tree()->latest()->paginte(15);

        return TreeResource::collection($tree);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreeRequest $request)
    {
        // 201 y 401,409, 422
        $tree = $request->user()->tree()->create($request->validated());

        return (new TreeResource($tree->load('effect')))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Tree $tree)
    {
        // 200 y 401,404
        $this->ensureUserOwnTree($request, $tree);

        return new TreeResource($tree->load('effect'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function ensureUserOwnsTree(Request $request, Tree $tree): void
    {
        abort_unless($tree->user_id === $request->user()->id, Response::HTTP_NOT_FOUND);
    }
}
