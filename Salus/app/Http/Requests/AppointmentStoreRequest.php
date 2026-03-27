<?php

namespace App\Http\Requests;

use App\Models\Appointment;

class AppointmentStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'status' => ['sometimes', 'in:' . implode(',', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_CANCELLED,
            ])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
