<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\ArenasContract;
use App\Http\Requests\ArenasRequest;
use Inertia\Inertia;
use Inertia\Response;

class ArenasController extends Controller
{
    public function __construct(private readonly ArenasContract $arenasRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Arenas/ArenasIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->arenasRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'nome' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(ArenasRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->arenasRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Arenas criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->arenasRepository->getById($id);
        return response()->json($registro);
    }

    public function update(ArenasRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->arenasRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Arenas atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->arenasRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Arenas excluído com sucesso!']);
    }
}
