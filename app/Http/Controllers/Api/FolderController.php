<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;


class FolderController extends Controller
{
    public function index()
    {
        return Folder::withCount('sets')->with('sets')->latest()->get();
    }

    public function store(StoreFolderRequest $request)
    {
        return Folder::create($request->validated());
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $folder->update($request->validated());
        return $folder;
    }

    public function destroy(Folder $folder)
    {
        $folder->delete();
        return response()->noContent();
    }
}
