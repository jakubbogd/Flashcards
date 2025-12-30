<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class FlashcardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => 'required|string|max:5000',
            'answer'   => 'required|string|max:5000',
            'notes' => 'nullable|string|max:10000',
            'image' => 'nullable|image|max:2048'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'question' => trim($this->question),
            'answer'   => trim($this->answer),
        ]);
    }
}
