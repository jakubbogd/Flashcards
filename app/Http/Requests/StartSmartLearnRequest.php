<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartSmartLearnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'set_ids' => ['required', 'array', 'min:1'],
            'set_ids.*' => ['exists:sets,id'],
            'count' => ['required', 'integer', 'min:1'],
        ];
    }
}
