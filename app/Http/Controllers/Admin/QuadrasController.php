<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\QuadrasContract;
use App\Http\Requests\QuadrasRequest;
use Illuminate\Support\Facades\Auth;

class QuadrasController extends Controller
{
    public function __construct(private readonly QuadrasContract $quadrasRepository)
    {
    }

    public function list(Request $request): JsonResponse
    {
        // Pega o usuário autenticado
        $user = Auth::user();

        // Se for arena, filtra apenas suas quadras
        if ($user && isset($user->id)) {
            $filters = $request->all();

            // Busca a arena do usuário autenticado
            $arena = \App\Databases\Models\Arenas::where('proprietario_id', $user->id)->first();

            if ($arena) {
                // Adiciona o filtro de arena_id automaticamente
                $filters['arena_id'] = $arena->id;
            }

            $dados = $this->quadrasRepository->paginate($filters)->toArray();
        } else {
            $dados = $this->quadrasRepository->paginate($request->all())->toArray();
        }

        $dados['filter_options'] = [
            'arena_id' => [
                'type' => 'text',
            ]
        ];

        return response()->json($dados);
    }

    public function create(QuadrasRequest $request): JsonResponse
    {
        // Pega o usuário autenticado via token Sanctum
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Busca a arena do usuário autenticado
        $arena = \App\Databases\Models\Arenas::where('proprietario_id', $user->id)->first();

        if (!$arena) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui uma arena cadastrada'
            ], 403);
        }

        // Prepara os dados
        $params = $request->except('_token');
        $params['arena_id'] = $arena->id;

        // Cria a quadra
        $this->quadrasRepository->create($params);

        return response()->json([
            'success' => true,
            'message' => 'Quadra criada com sucesso!',
            'data' => [
                'arena_id' => $params['arena_id'],
                'nome' => $params['nome']
            ]
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->quadrasRepository->getById($id);

        // Busca a arena do usuário autenticado
        $arena = \App\Databases\Models\Arenas::where('proprietario_id', $user->id)->first();

        // Verifica se a quadra pertence à arena autenticada
        if ($user && $arena && $registro->arena_id !== $arena->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar esta quadra'
            ], 403);
        }

        return response()->json($registro);
    }

    public function update(QuadrasRequest $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->quadrasRepository->getById($id);

        // Busca a arena do usuário autenticado
        $arena = \App\Databases\Models\Arenas::where('proprietario_id', $user->id)->first();

        // Verifica se a quadra pertence à arena autenticada
        if ($user && $arena && $registro->arena_id !== $arena->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para atualizar esta quadra'
            ], 403);
        }

        $params = $request->except('_token');
        // Garante que o arena_id não seja alterado
        unset($params['arena_id']);

        $this->quadrasRepository->update($id, $params);

        return response()->json([
            'success' => true,
            'message' => 'Quadra atualizada com sucesso!'
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->quadrasRepository->getById($id);

        // Busca a arena do usuário autenticado
        $arena = \App\Databases\Models\Arenas::where('proprietario_id', $user->id)->first();

        // Verifica se a quadra pertence à arena autenticada
        if ($user && $arena && $registro->arena_id !== $arena->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir esta quadra'
            ], 403);
        }

        $this->quadrasRepository->destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Quadra excluída com sucesso!'
        ]);
    }
}
