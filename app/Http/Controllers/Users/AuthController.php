<?php

namespace App\Http\Controllers\Users;

use App\Databases\Models\Arenas;
use App\Databases\Models\Jogadores;
use App\Databases\Models\Professores;
use App\Http\Controllers\Controller;
use App\Databases\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login do usuário e geração do token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Users::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Revoga todos os tokens anteriores do usuário
        $user->tokens()->delete();

        // Cria um novo token
        $token = $user->createToken('mobile-app')->plainTextToken;
        if ($user->tipo_usuario === 'jogador'){
            $subtipo = Jogadores::where('user_id', $user->id)->first();
        }
        if ($user->tipo_usuario === 'arena'){
            $subtipo = Arenas::where('proprietario_id', $user->id)->first();
        }
        if ($user->tipo_usuario === 'professor'){
            $subtipo = Professores::where('user_id', $user->id)->first();
        }
        return response()->json([
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tipo_usuario' => $user->tipo_usuario,
                    'subtipo' => $subtipo ?? null,
                ],
                'token' => $token,
            ],
        ], 200);
    }

    public function teste(Request $request): JsonResponse
    {
        return response()->json(['oi']);
    }


    /**
     * Logout do usuário (revoga o token atual)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoga o token atual usado na requisição
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ], 200);
    }

    /**
     * Retorna os dados do usuário autenticado
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ], 200);
    }

    /**
     * Registro de novo usuário
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Users::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ],
        ], 201);
    }
}
