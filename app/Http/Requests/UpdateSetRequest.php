<?php

namespace App\Http\Requests;

use App\Http\Requests\StoreSetRequest;

class UpdateSetRequest extends StoreSetRequest
{
    public function rules(): array
    {
        // Podstawowe reguły jak przy tworzeniu, bez folder_id
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
