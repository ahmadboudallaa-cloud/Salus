<?php

namespace App\Http\Controllers\Api;

use App\Models\AiAdvice;
use App\Models\Symptom;
use App\Services\AiHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
class AiHealthController extends ApiController
{
    public function __construct(private readonly AiHealthService $aiHealthService)
    {
    }

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

    public function history(Request $request): JsonResponse
    {
        $history = AiAdvice::where('user_id', $request->user()->id)
            ->orderByDesc('generated_at')
            ->get();

        return $this->success($history, 'Historique des conseils IA');
    }
}
