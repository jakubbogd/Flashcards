<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnswerSmartLearnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode' => ['required', 'in:multiple_choice,write'],

            'option_txt' => ['required', 'string'],

            'option_id' => [
                Rule::requiredIf($this->mode === 'multiple_choice'),
                'integer',
                Rule::when(
                    $this->mode === 'multiple_choice',
                    ['exists:flashcard_options,id']
                ),
            ],
        ];
    }
}
