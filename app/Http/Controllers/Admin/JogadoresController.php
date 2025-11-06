<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\JogadoresContract;
use App\Http\Requests\JogadoresRequest;
use Inertia\Inertia;
use Inertia\Response;

class JogadoresController extends Controller
{
    public function __construct(private readonly JogadoresContract $jogadoresRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Jogadores/JogadoresIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->jogadoresRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'user_id' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(JogadoresRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->jogadoresRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Jogadores criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->jogadoresRepository->getById($id);
        return response()->json($registro);
    }

    public function update(JogadoresRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->jogadoresRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Jogadores atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->jogadoresRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Jogadores excluído com sucesso!']);
    }
}
