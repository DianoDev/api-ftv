<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\JogadoresContract;
use App\Http\Requests\JogadoresRequest;
use Illuminate\Support\Facades\Auth;

class JogadorController extends Controller
{
    public function __construct(private readonly JogadoresContract $jogadoresRepository)
    {
    }

    /**
     * Retorna o perfil do jogador autenticado
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Buscar dados do jogador pelo user_id
        $jogador = \App\Databases\Models\Jogadores::where('user_id', $user->id)->first();

        if (!$jogador) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de jogador não encontrado',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $jogador
        ]);
    }

    /**
     * Criar perfil de jogador para o usuário autenticado
     */
    public function create(JogadoresRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Verificar se já existe um perfil de jogador
        $existente = \App\Databases\Models\Jogadores::where('user_id', $user->id)->first();
        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui um perfil de jogador'
            ], 400);
        }

        $params = $request->validated();
        $params['user_id'] = $user->id;

        try {
            $this->jogadoresRepository->create($params);

            return response()->json([
                'success' => true,
                'message' => 'Perfil de jogador criado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar perfil do jogador autenticado
     */
    public function update(JogadoresRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Buscar perfil do jogador
        $jogador = \App\Databases\Models\Jogadores::where('user_id', $user->id)->first();

        if (!$jogador) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de jogador não encontrado'
            ], 404);
        }

        $params = $request->validated();

        try {
            $this->jogadoresRepository->update($jogador->id, $params);

            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar jogador por ID (público)
     */
    public function show(int $id): JsonResponse
    {
        try {
            $jogador = $this->jogadoresRepository->getById($id);

            return response()->json([
                'success' => true,
                'data' => $jogador
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jogador não encontrado'
            ], 404);
        }
    }

    /**
     * Listar jogadores com filtros
     */
    public function list(Request $request): JsonResponse
    {
        $dados = $this->jogadoresRepository->paginate($request->all())->toArray();

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    /**
     * Buscar ranking de jogadores
     */
    public function ranking(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $jogadores = \App\Databases\Models\Jogadores::query()
            ->orderBy('ranking', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $jogadores
        ]);
    }
}
