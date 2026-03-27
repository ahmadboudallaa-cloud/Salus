<?php

namespace App\Http\Requests;

class SymptomUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'severity' => ['sometimes', 'required', 'in:mild,moderate,severe'],
            'description' => ['nullable', 'string'],
            'date_recorded' => ['sometimes', 'required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
