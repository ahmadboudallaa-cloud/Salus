<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SymptomStoreRequest;
use App\Http\Requests\SymptomUpdateRequest;
use App\Models\Symptom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
class SymptomController extends ApiController
{
    /**
     * @OA\PathItem(
     *     path="/api/symptoms",
     *     @OA\Get(
     *         tags={"Symptoms"},
     *         summary="Lister les symptomes",
     *         security={{"sanctum":{}}},
     *         @OA\Response(
     *             response=200,
     *             description="Liste des symptomes",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Liste des symptomes"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Symptom")
     *                 )
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie")
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $symptoms = Symptom::where('user_id', $request->user()->id)
            ->orderByDesc('date_recorded')
            ->get();

        return $this->success($symptoms, 'Liste des symptomes');
    }

    /**
     * @OA\PathItem(
     *     path="/api/symptoms",
     *     @OA\Post(
     *         tags={"Symptoms"},
     *         summary="Ajouter un symptome",
     *         security={{"sanctum":{}}},
     *         @OA\RequestBody(
     *             required=true,
     *             @OA\JsonContent(
     *                 required={"name","severity","date_recorded"},
     *                 @OA\Property(property="name", type="string", example="Fievre"),
     *                 @OA\Property(property="severity", type="string", example="moderate"),
     *                 @OA\Property(property="description", type="string", example="Fievre depuis 2 jours"),
     *                 @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *                 @OA\Property(property="notes", type="string", example="Prise de paracetamol")
     *             )
     *         ),
     *         @OA\Response(
     *             response=201,
     *             description="Symptome ajoute",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Symptome ajoute"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Symptom")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=422, description="Validation echouee")
     *     )
     * )
     */
    public function store(SymptomStoreRequest $request): JsonResponse
    {
        $symptom = Symptom::create([
            'user_id' => $request->user()->id,
            'name' => $request->input('name'),
            'severity' => $request->input('severity'),
            'description' => $request->input('description'),
            'date_recorded' => $request->input('date_recorded'),
            'notes' => $request->input('notes'),
        ]);

        return $this->success($symptom, 'Symptome ajoute', 201);
    }

    /**
     * @OA\PathItem(
     *     path="/api/symptoms/{id}",
     *     @OA\Get(
     *         tags={"Symptoms"},
     *         summary="Voir un symptome",
     *         security={{"sanctum":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Detail du symptome",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Detail du symptome"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Symptom")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=404, description="Symptome introuvable")
     *     )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $symptom = Symptom::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $symptom) {
            return $this->error([], 'Symptome introuvable', 404);
        }

        return $this->success($symptom, 'Detail du symptome');
    }

    /**
     * @OA\PathItem(
     *     path="/api/symptoms/{id}",
     *     @OA\Put(
     *         tags={"Symptoms"},
     *         summary="Mettre a jour un symptome",
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
     *                 @OA\Property(property="name", type="string", example="Fievre"),
     *                 @OA\Property(property="severity", type="string", example="low"),
     *                 @OA\Property(property="description", type="string", example="Fievre en baisse"),
     *                 @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
     *                 @OA\Property(property="notes", type="string", example="Hydratation")
     *             )
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Symptome mis a jour",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Symptome mis a jour"),
     *                 @OA\Property(property="data", ref="#/components/schemas/Symptom")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=404, description="Symptome introuvable"),
     *         @OA\Response(response=422, description="Validation echouee")
     *     )
     * )
     */
    public function update(SymptomUpdateRequest $request, int $id): JsonResponse
    {
        $symptom = Symptom::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $symptom) {
            return $this->error([], 'Symptome introuvable', 404);
        }

        $symptom->update($request->validated());

        return $this->success($symptom, 'Symptome mis a jour');
    }

    /**
     * @OA\PathItem(
     *     path="/api/symptoms/{id}",
     *     @OA\Delete(
     *         tags={"Symptoms"},
     *         summary="Supprimer un symptome",
     *         security={{"sanctum":{}}},
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             required=true,
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Symptome supprime",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Symptome supprime"),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=404, description="Symptome introuvable")
     *     )
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $symptom = Symptom::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $symptom) {
            return $this->error([], 'Symptome introuvable', 404);
        }

        $symptom->delete();

        return $this->success((object) [], 'Symptome supprime');
    }
}
