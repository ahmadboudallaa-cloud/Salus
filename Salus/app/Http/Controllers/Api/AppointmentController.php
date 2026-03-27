<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AppointmentStoreRequest;
use App\Http\Requests\AppointmentUpdateRequest;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
class AppointmentController extends ApiController
{
    /**
     * @OA\PathItem(
     *     path="/api/appointments",
     *     @OA\Get(
     *         tags={"Appointments"},
     *         summary="Lister les rendez-vous",
     *         security={{"sanctum":{}}},
     *         @OA\Response(
     *             response=200,
     *             description="Liste des rendez-vous",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Liste des rendez-vous"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Appointment")
     *                 )
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = Appointment::with('doctor')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('appointment_date')
            ->get();

        return $this->success($appointments, 'Liste des rendez-vous');
    }

    /**
     * @OA\PathItem(
     *     path="/api/appointments",
     *     @OA\Post(
     *         tags={"Appointments"},
     *         summary="Creer un rendez-vous",
     *         security={{"sanctum":{}}},
     *         @OA\RequestBody(
     *             required=true,
     *             @OA\JsonContent(
     *                 required={"doctor_id","appointment_date"},
     *                 @OA\Property(property="doctor_id", type="integer", example=3),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-04-02T09:30:00Z"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="notes", type="string", example="Premiere consultation")
     *             )
     *         ),
     *         @OA\Response(
     *             response=201,
     *             description="Rendez-vous cree",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Rendez-vous cree"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Appointment")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=422, description="Validation echouee")
     *     )
     * )
     */
    public function store(AppointmentStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? Appointment::STATUS_PENDING;

        $appointment = Appointment::create($data);

        return $this->success($appointment, 'Rendez-vous cree', 201);
    }

    /**
     * @OA\PathItem(
     *     path="/api/appointments/{id}",
     *     @OA\Get(
     *         tags={"Appointments"},
     *         summary="Voir un rendez-vous",
     *         security={{"sanctum":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Detail du rendez-vous",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Detail du rendez-vous"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Appointment")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=404, description="Rendez-vous introuvable")
     *     )
     * )
     */
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

    /**
     * @OA\PathItem(
     *     path="/api/appointments/{id}",
     *     @OA\Put(
     *         tags={"Appointments"},
     *         summary="Mettre a jour un rendez-vous",
     *         security={{"sanctum":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\RequestBody(
     *             required=true,
     *             @OA\JsonContent(
     *                 @OA\Property(property="doctor_id", type="integer", example=3),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-04-02T09:30:00Z"),
     *                 @OA\Property(property="status", type="string", example="confirmed"),
     *                 @OA\Property(property="notes", type="string", example="Confirme par le medecin")
     *             )
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Rendez-vous mis a jour",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Rendez-vous mis a jour"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Appointment")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=404, description="Rendez-vous introuvable"),
     *         @OA\Response(response=422, description="Validation echouee")
     *     )
     * )
     */
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

    /**
     * @OA\PathItem(
     *     path="/api/appointments/{id}",
     *     @OA\Delete(
     *         tags={"Appointments"},
     *         summary="Annuler un rendez-vous",
     *         security={{"sanctum":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Rendez-vous annule",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Rendez-vous annule"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Appointment")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=404, description="Rendez-vous introuvable")
     *     )
     * )
     */
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
