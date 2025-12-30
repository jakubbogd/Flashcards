<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportFlashcardsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // lub dodaj autoryzację, jeśli potrzebna
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
        ];
    }
}
