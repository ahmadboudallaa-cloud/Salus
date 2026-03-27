<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class DoctorController extends ApiController
{
    public function index(): JsonResponse
    {
        $doctors = Doctor::orderBy('name')->get();

        return $this->success($doctors, 'Liste des medecins');
    }

    public function show(int $id): JsonResponse
    {
        $doctor = Doctor::find($id);

        if (! $doctor) {
            return $this->error([], 'Medecin introuvable', 404);
        }

        return $this->success($doctor, 'Detail du medecin');
    }

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
