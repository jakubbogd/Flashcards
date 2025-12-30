<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Http\Requests\StoreSetRequest;
use App\Http\Requests\UpdateSetRequest;

class SetController extends Controller
{
    public function index()
    {
        return Set::withCount('flashcards')->latest()->get();
    }

    public function store(StoreSetRequest $request)
    {
        $set = Set::create($request->validated());
        return response()->json($set, 201);
    }

    public function show(Set $set)
    {
        return $set->load('flashcards');
    }

    public function update(UpdateSetRequest $request, Set $set)
    {
        $set->update($request->validated());
        return response()->json($set);
    }

    public function destroy(Set $set)
    {
        $set->flashcards()->delete();
        $set->delete();
        return response()->json(null, 204);
    }
}
