<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AppointmentStoreRequest;
use App\Http\Requests\AppointmentUpdateRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class AppointmentController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::with('doctor')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('appointment_date')
            ->get();

        return $this->success($appointments, 'Liste des rendez-vous');
    }

    public function store(AppointmentStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? Appointment::STATUS_PENDING;

        $appointment = Appointment::create($data);

        return $this->success($appointment, 'Rendez-vous cree', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::with('doctor')
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $appointment) {
            return $this->error([], 'Rendez-vous introuvable', 404);
        }

        return $this->success($appointment, 'Detail du rendez-vous');
    }

    public function update(AppointmentUpdateRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $appointment) {
            return $this->error([], 'Rendez-vous introuvable', 404);
        }

        $appointment->update($request->validated());

        return $this->success($appointment->fresh('doctor'), 'Rendez-vous mis a jour');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $appointment) {
            return $this->error([], 'Rendez-vous introuvable', 404);
        }

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return $this->success($appointment->fresh('doctor'), 'Rendez-vous annule');
    }
}
