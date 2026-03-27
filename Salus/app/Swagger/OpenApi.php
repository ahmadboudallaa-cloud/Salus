<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Salus API",
 *     version="1.0.0",
 *     description="Documentation de l'API Salus."
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Serveur principal"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Entrez le token au format: Bearer <token>"
 * )
 * @OA\Tag(name="Auth", description="Authentification")
 * @OA\Tag(name="Doctors", description="Medecins")
 * @OA\Tag(name="Symptoms", description="Symptomes")
 * @OA\Tag(name="Appointments", description="Rendez-vous")
 * @OA\Tag(name="AI", description="Conseils IA")
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Jane Doe"),
 *     @OA\Property(property="email", type="string", example="jane@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-27T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-27T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="Doctor",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dr. Amina El Idrissi"),
 *     @OA\Property(property="specialty", type="string", example="Cardiologie"),
 *     @OA\Property(property="city", type="string", example="Casablanca"),
 *     @OA\Property(property="consultation_price", type="string", example="350.00"),
 *     @OA\Property(property="available_days", type="array", @OA\Items(type="string", example="Monday")),
 *     @OA\Property(property="yearsofexperience", type="integer", example=10)
 * )
 *
 * @OA\Schema(
 *     schema="Symptom",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Fievre"),
 *     @OA\Property(property="severity", type="string", example="moderate"),
 *     @OA\Property(property="description", type="string", example="Fievre depuis 2 jours"),
 *     @OA\Property(property="date_recorded", type="string", format="date", example="2026-03-26"),
 *     @OA\Property(property="notes", type="string", example="Prise de paracetamol")
 * )
 *
 * @OA\Schema(
 *     schema="Appointment",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="doctor_id", type="integer", example=3),
 *     @OA\Property(property="appointment_date", type="string", format="date-time", example="2026-04-02T09:30:00Z"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="notes", type="string", example="Premiere consultation"),
 *     @OA\Property(property="doctor", ref="#/components/schemas/Doctor")
 * )
 *
 * @OA\Schema(
 *     schema="AiAdvice",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="advice", type="string", example="Reposez-vous et hydratez-vous."),
 *     @OA\Property(property="generated_at", type="string", format="date-time", example="2026-03-27T12:00:00Z"),
 *     @OA\Property(property="symptoms_snapshot", type="array", @OA\Items(type="object"))
 * )
 */
final class OpenApi
{
}
