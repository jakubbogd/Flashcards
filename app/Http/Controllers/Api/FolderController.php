<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(User $user)
    {
        return Folder::where('user_id', Auth::id())->withCount('sets')->with('sets')->latest()->get();
    }

    public function store(StoreFolderRequest $request)
    {
        $validated = $request->validated();
        Log::info(1);
        return Folder::create(['user_id' => Auth::id(), 'name' => $validated['name']]);
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
