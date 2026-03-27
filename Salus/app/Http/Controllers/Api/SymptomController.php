<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SymptomStoreRequest;
use App\Http\Requests\SymptomUpdateRequest;
use App\Models\Symptom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class SymptomController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $symptoms = Symptom::where('user_id', $request->user()->id)
            ->orderByDesc('date_recorded')
            ->get();

        return $this->success($symptoms, 'Liste des symptomes');
    }

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
