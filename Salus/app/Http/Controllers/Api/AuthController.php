<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
class AuthController extends ApiController
{
    /**
     * @OA\PathItem(
     *     path="/api/register",
     *     @OA\Post(
     *         tags={"Auth"},
     *         summary="Inscription utilisateur",
     *         @OA\RequestBody(
     *             required=true,
     *             @OA\JsonContent(
     *                 required={"name","email","password"},
     *                 @OA\Property(property="name", type="string", example="Jane Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="secret123")
     *             )
     *         ),
     *         @OA\Response(
     *             response=201,
     *             description="Inscription reussie",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Inscription reussie"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="user", ref="#/components/schemas/User"),
     *                     @OA\Property(property="token", type="string", example="1|token")
     *                 )
     *             )
     *         ),
     *         @OA\Response(response=422, description="Validation echouee")
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Inscription reussie', 201);
    }

    /**
     * @OA\PathItem(
     *     path="/api/login",
     *     @OA\Post(
     *         tags={"Auth"},
     *         summary="Connexion utilisateur",
     *         @OA\RequestBody(
     *             required=true,
     *             @OA\JsonContent(
     *                 required={"email","password"},
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="secret123")
     *             )
     *         ),
     *         @OA\Response(
     *             response=200,
     *             description="Connexion reussie",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Connexion reussie"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="user", ref="#/components/schemas/User"),
     *                     @OA\Property(property="token", type="string", example="1|token")
     *                 )
     *             )
     *         ),
     *         @OA\Response(response=401, description="Identifiants invalides"),
     *         @OA\Response(response=422, description="Validation echouee")
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->error(['email' => ['Identifiants invalides']], 'Identifiants invalides', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Connexion reussie');
    }

    /**
     * @OA\PathItem(
     *     path="/api/logout",
     *     @OA\Post(
     *         tags={"Auth"},
     *         summary="Deconnexion utilisateur",
     *         security={{"sanctum":{}}},
     *         @OA\Response(
     *             response=200,
     *             description="Deconnexion reussie",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Deconnexion reussie"),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie")
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return $this->success((object) [], 'Deconnexion reussie');
    }

    /**
     * @OA\PathItem(
     *     path="/api/me",
     *     @OA\Get(
     *         tags={"Auth"},
     *         summary="Profil utilisateur",
     *         security={{"sanctum":{}}},
     *         @OA\Response(
     *             response=200,
     *             description="Profil utilisateur",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Profil utilisateur"),
     *                 @OA\Property(property="data", ref="#/components/schemas/User")
     *             )
     *         ),
     *         @OA\Response(response=401, description="Non authentifie")
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user(), 'Profil utilisateur');
    }
}
