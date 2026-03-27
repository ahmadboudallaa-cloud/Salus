<?php

namespace App\Http\Requests;

use App\Models\Appointment;

class AppointmentUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'doctor_id' => ['sometimes', 'required', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['sometimes', 'required', 'date', 'after:now'],
            'status' => ['sometimes', 'required', 'in:' . implode(',', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_CANCELLED,
            ])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
