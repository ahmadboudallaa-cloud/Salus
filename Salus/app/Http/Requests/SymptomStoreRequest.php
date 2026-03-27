<?php

namespace App\Http\Requests;

class SymptomStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'severity' => ['required', 'in:mild,moderate,severe'],
            'description' => ['nullable', 'string'],
            'date_recorded' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
