<?php

namespace App\Http\Controllers\Users;

use App\Databases\Contracts\AuthContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Injeção de dependência da interface do repository
     */
    public function __construct(private readonly AuthContract $authRepository)
    {

    }

    /**
     * Login do usuário
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Tenta autenticar o usuário usando o repository
            $user = $this->authRepository->attemptLogin(
                $request->email,
                $request->password
            );

            // Se a autenticação falhar
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou senha incorretos'
                ], 401);
            }

            // Autenticação bem-sucedida
            // TODO: Aqui você pode gerar um token (JWT, Sanctum, etc.)
            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nome' => $user->nome,
                        'email' => $user->email,
                        'tipo_usuario' => $user->tipo_usuario,
                    ],
                    // TODO: Adicionar token aqui
                    // 'token' => $token,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout do usuário
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // TODO: Implementar lógica de logout
            // Invalidar token, limpar sessão, etc.

            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter dados do usuário autenticado
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            // TODO: Implementar lógica para pegar usuário do token
            // $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    // 'user' => $user
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados do usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
