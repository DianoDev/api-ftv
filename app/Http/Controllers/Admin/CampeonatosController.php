<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\CampeonatosContract;
use App\Http\Requests\CampeonatosRequest;
use Illuminate\Support\Facades\Auth;

class CampeonatosController extends Controller
{
    public function __construct(private readonly CampeonatosContract $campeonatosRepository)
    {
    }

    public function list(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Se for organizador, filtra apenas seus campeonatos
        if ($user && isset($user->id)) {
            $filters = $request->all();
            $filters['organizador_id'] = $user->id;

            $dados = $this->campeonatosRepository->paginate($filters)->toArray();
        } else {
            $dados = $this->campeonatosRepository->paginate($request->all())->toArray();
        }

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

    public function create(CampeonatosRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $params = $request->except('_token');
        // Define o organizador_id do usuário autenticado
        $params['organizador_id'] = $user->id;

        // Valida as datas
        if (isset($params['data_fim']) && isset($params['data_inicio'])) {
            if (strtotime($params['data_fim']) < strtotime($params['data_inicio'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A data de fim deve ser posterior à data de início'
                ], 422);
            }
        }

        $this->campeonatosRepository->create($params);

        return response()->json([
            'success' => true,
            'message' => 'Campeonato criado com sucesso!',
            'data' => [
                'organizador_id' => $params['organizador_id'],
                'nome' => $params['nome']
            ]
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->campeonatosRepository->getById($id);


        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    public function update(CampeonatosRequest $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->campeonatosRepository->getById($id);

        // Verifica se o campeonato pertence ao organizador autenticado
        if ($user && $registro->organizador_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para atualizar este campeonato'
            ], 403);
        }

        $params = $request->except('_token');

        // Garante que o organizador_id não seja alterado
        unset($params['organizador_id']);

        // Valida as datas se ambas forem fornecidas
        if (isset($params['data_fim']) && isset($params['data_inicio'])) {
            if (strtotime($params['data_fim']) < strtotime($params['data_inicio'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A data de fim deve ser posterior à data de início'
                ], 422);
            }
        }

        $this->campeonatosRepository->update($id, $params);

        return response()->json([
            'success' => true,
            'message' => 'Campeonato atualizado com sucesso!'
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->campeonatosRepository->getById($id);

        // Verifica se o campeonato pertence ao organizador autenticado
        if ($user && $registro->organizador_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir este campeonato'
            ], 403);
        }

        $this->campeonatosRepository->destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Campeonato excluído com sucesso!'
        ]);
    }
}
