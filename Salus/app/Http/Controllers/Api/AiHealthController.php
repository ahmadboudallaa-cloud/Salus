<?php

namespace App\Http\Controllers\Api;

use App\Models\AiAdvice;
use App\Models\Symptom;
use App\Services\AiHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use OpenApi\Annotations as OA;
class AiHealthController extends ApiController
{
    public function __construct(private readonly AiHealthService $aiHealthService)
    {
    }

    /**
     * @OA\PathItem(
     *     path="/api/ai/health-advice",
     *     @OA\Post(
     *         tags={"AI"},
     *         summary="Generer un conseil IA",
     *         security={{"sanctum":{}}},
     *         @OA\Response(
     *             response=200,
     *             description="Conseils generes",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Conseils generes"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="advice", type="string", example="Reposez-vous et hydratez-vous."),
     *                     @OA\Property(property="generated_at", type="string", format="date-time", example="2026-03-27T12:00:00Z")
     *                 )
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie"),
     *         @OA\Response(response=422, description="Aucun symptome recent a analyser"),
     *         @OA\Response(response=502, description="Erreur IA")
     *     )
     * )
     */
    public function generate(Request $request): JsonResponse
    {
        $symptoms = Symptom::where('user_id', $request->user()->id)
            ->orderByDesc('date_recorded')
            ->limit(10)
            ->get();

        if ($symptoms->isEmpty()) {
            return $this->error([], 'Aucun symptome recent a analyser', 422);
        }

        try {
            $advice = $this->aiHealthService->generateAdvice($symptoms);
        } catch (Throwable $exception) {
            return $this->error([], $exception->getMessage(), 502);
        }

        $snapshot = $symptoms->map(function ($symptom): array {
            return [
                'id' => $symptom->id,
                'name' => $symptom->name,
                'severity' => $symptom->severity,
                'date_recorded' => $symptom->date_recorded?->format('Y-m-d'),
                'notes' => $symptom->notes,
            ];
        })->values()->all();

        $aiAdvice = AiAdvice::create([
            'user_id' => $request->user()->id,
            'advice' => $advice,
            'generated_at' => now(),
            'symptoms_snapshot' => $snapshot,
        ]);

        return $this->success([
            'advice' => $aiAdvice->advice,
            'generated_at' => $aiAdvice->generated_at,
        ], 'Conseils generes');
    }

    /**
     * @OA\PathItem(
     *     path="/api/ai/health-advice/history",
     *     @OA\Get(
     *         tags={"AI"},
     *         summary="Historique des conseils IA",
     *         security={{"sanctum":{}}},
     *         @OA\Response(
     *             response=200,
     *             description="Historique des conseils IA",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Historique des conseils IA"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/AiAdvice")
     *                 )
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie")
     *     )
     * )
     */
    public function history(Request $request): JsonResponse
    {
        $history = AiAdvice::where('user_id', $request->user()->id)
            ->orderByDesc('generated_at')
            ->get();

        return $this->success($history, 'Historique des conseils IA');
    }
}
