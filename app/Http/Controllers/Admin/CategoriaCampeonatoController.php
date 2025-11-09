<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\CategoriaCampeonatoContract;
use App\Databases\Contracts\CampeonatosContract;
use App\Http\Requests\CategoriaCampeonatoRequest;
use Illuminate\Support\Facades\Auth;

class CategoriaCampeonatoController extends Controller
{
    public function __construct(
        private readonly CategoriaCampeonatoContract $categoriasRepository,
        private readonly CampeonatosContract $campeonatosRepository
    ) {
    }

    public function list(Request $request): JsonResponse
    {
        $user = Auth::user();
        $campeonatoId = $request->input('campeonato_id');

        // Verifica se o campeonato_id foi fornecido
        if (!$campeonatoId) {
            return response()->json([
                'success' => false,
                'message' => 'ID do campeonato não fornecido'
            ], 400);
        }

        // Verifica se o campeonato existe e pertence ao usuário
        try {
            $campeonato = $this->campeonatosRepository->getById($campeonatoId);

            if ($user && $campeonato->organizador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar este campeonato'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Campeonato não encontrado'
            ], 404);
        }

        $filters = $request->all();
        $filters['campeonato_id'] = $campeonatoId;

        $dados = $this->categoriasRepository->paginate($filters)->toArray();

        $dados['filter_options'] = [
            'nome' => [
                'type' => 'text',
            ],
            'status' => [
                'type' => 'select',
                'options' => [
                    'inscricoes_abertas' => 'Inscrições Abertas',
                    'em_andamento' => 'Em Andamento',
                    'finalizado' => 'Finalizado',
                    'cancelado' => 'Cancelado',
                ]
            ]
        ];

        return response()->json($dados);
    }

    public function create(CategoriaCampeonatoRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $params = $request->except('_token');

        // Verifica se o campeonato existe e pertence ao usuário
        try {
            $campeonato = $this->campeonatosRepository->getById($params['campeonato_id']);

            if ($campeonato->organizador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para adicionar categorias neste campeonato'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Campeonato não encontrado'
            ], 404);
        }

        $this->categoriasRepository->create($params);

        return response()->json([
            'success' => true,
            'message' => 'Categoria criada com sucesso!',
            'data' => [
                'campeonato_id' => $params['campeonato_id'],
                'nome' => $params['nome']
            ]
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $user = Auth::user();

        try {
            $categoria = $this->categoriasRepository->getById($id);
            $campeonato = $this->campeonatosRepository->getById($categoria->campeonato_id);

            // Verifica se o campeonato pertence ao usuário autenticado
            if ($user && $campeonato->organizador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para acessar esta categoria'
                ], 403);
            }

            return response()->json($categoria);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }
    }

    public function update(CategoriaCampeonatoRequest $request, int $id): JsonResponse
    {
        $user = Auth::user();

        try {
            $categoria = $this->categoriasRepository->getById($id);
            $campeonato = $this->campeonatosRepository->getById($categoria->campeonato_id);

            // Verifica se o campeonato pertence ao usuário autenticado
            if ($user && $campeonato->organizador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para atualizar esta categoria'
                ], 403);
            }

            $params = $request->except('_token');

            // Garante que o campeonato_id não seja alterado
            unset($params['campeonato_id']);

            $this->categoriasRepository->update($id, $params);

            return response()->json([
                'success' => true,
                'message' => 'Categoria atualizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar categoria'
            ], 500);
        }
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        try {
            $categoria = $this->categoriasRepository->getById($id);
            $campeonato = $this->campeonatosRepository->getById($categoria->campeonato_id);

            // Verifica se o campeonato pertence ao usuário autenticado
            if ($user && $campeonato->organizador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para excluir esta categoria'
                ], 403);
            }

            $this->categoriasRepository->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Categoria excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir categoria'
            ], 500);
        }
    }
}
