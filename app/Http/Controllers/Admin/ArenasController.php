<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\ArenasContract;
use App\Http\Requests\ArenasRequest;
use Illuminate\Support\Facades\Auth;

class ArenasController extends Controller
{
    public function __construct(private readonly ArenasContract $arenasRepository)
    {
    }

    public function list(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Se for proprietário, filtra apenas suas arenas
        if ($user && isset($user->id)) {
            $filters = $request->all();
            $filters['proprietario_id'] = $user->id;

            $dados = $this->arenasRepository->paginate($filters)->toArray();
        } else {
            $dados = $this->arenasRepository->paginate($request->all())->toArray();
        }

        $dados['filter_options'] = [
            'nome' => [
                'type' => 'text',
            ],
            'cidade' => [
                'type' => 'text',
            ]
        ];

        return response()->json($dados);
    }

    public function create(ArenasRequest $request): JsonResponse
    {
        // Pega o usuário autenticado via token Sanctum
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $params = $request->except('_token');
        // Define o proprietario_id do usuário autenticado
        $params['proprietario_id'] = $user->id;

        // Define ativo como true por padrão
        $params['ativo'] = true;

        // Inicializa campos numéricos padrão
        $params['rating'] = 0;
        $params['total_avaliacoes'] = 0;

        // Cria a arena
        $this->arenasRepository->create($params);

        return response()->json([
            'success' => true,
            'message' => 'Arena criada com sucesso!',
            'data' => [
                'proprietario_id' => $params['proprietario_id'],
                'nome' => $params['nome']
            ]
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->arenasRepository->getById($id);

        // Verifica se a arena pertence ao proprietário autenticado
        if ($user && $registro->proprietario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar esta arena'
            ], 403);
        }

        return response()->json($registro);
    }

    public function update(ArenasRequest $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->arenasRepository->getById($id);

        // Verifica se a arena pertence ao proprietário autenticado
        if ($user && $registro->proprietario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para atualizar esta arena'
            ], 403);
        }

        // Garante que o proprietario_id não seja alterado
        unset($params['proprietario_id']);

        $this->arenasRepository->update($id, $params);

        return response()->json([
            'success' => true,
            'message' => 'Arena atualizada com sucesso!'
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $registro = $this->arenasRepository->getById($id);

        // Verifica se a arena pertence ao proprietário autenticado
        if ($user && $registro->proprietario_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir esta arena'
            ], 403);
        }

        $this->arenasRepository->destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Arena excluída com sucesso!'
        ]);
    }
}
