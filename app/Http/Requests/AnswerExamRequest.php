<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Możesz tu dodać autoryzację jeśli trzeba
    }

    public function rules(): array
    {
        return [
            'is_correct' => ['required', 'boolean'],
        ];
    }
}
