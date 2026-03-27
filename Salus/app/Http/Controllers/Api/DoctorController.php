<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
class DoctorController extends ApiController
{
    /**
     * @OA\PathItem(
     *     path="/api/doctors",
     *     @OA\Get(
     *         tags={"Doctors"},
     *         summary="Lister les medecins",
     *         @OA\Response(
     *             response=200,
     *             description="Liste des medecins",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Liste des medecins"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Doctor")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $doctors = Doctor::orderBy('name')->get();

        return $this->success($doctors, 'Liste des medecins');
    }

    /**
     * @OA\PathItem(
     *     path="/api/doctors/{id}",
     *     @OA\Get(
     *         tags={"Doctors"},
     *         summary="Voir un medecin",
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Detail du medecin",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Detail du medecin"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Doctor")
     *             )
     *         ),
     *         @OA\Response(response=404, description="Medecin introuvable")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $doctor = Doctor::find($id);

        if (! $doctor) {
            return $this->error([], 'Medecin introuvable', 404);
        }

        return $this->success($doctor, 'Detail du medecin');
    }

    /**
     * @OA\PathItem(
     *     path="/api/doctors/search",
     *     @OA\Get(
     *         tags={"Doctors"},
     *         summary="Rechercher des medecins",
     *         @OA\Parameter(
     *             name="specialty",
     *             in="query",
     *             required=false,
     *             @OA\Schema(type="string", example="Cardiologie")
     *         ),
     *         @OA\Parameter(
     *             name="city",
     *             in="query",
     *             required=false,
     *             @OA\Schema(type="string", example="Casablanca")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Resultats de recherche",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Resultats de recherche"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Doctor")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $query = Doctor::query();

        if ($request->filled('specialty')) {
            $query->where('specialty', 'like', '%' . $request->input('specialty') . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->input('city') . '%');
        }

        $doctors = $query->orderBy('name')->get();

        return $this->success($doctors, 'Resultats de recherche');
    }
}
