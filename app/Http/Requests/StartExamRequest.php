<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // możesz dodać autoryzację jeśli potrzebna
    }

    public function rules(): array
    {
        return [
            'set_ids' => ['required', 'array', 'min:1'],
            'set_ids.*' => ['exists:sets,id'],
            'difficulty' => ['required', 'in:easy,normal,hard'],
        ];
    }
}
