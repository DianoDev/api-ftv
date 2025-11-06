<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\AulasContract;
use App\Http\Requests\AulasRequest;
use Inertia\Inertia;
use Inertia\Response;

class AulasController extends Controller
{
    public function __construct(private readonly AulasContract $aulasRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Aulas/AulasIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->aulasRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'data_aula' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(AulasRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->aulasRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Aulas criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->aulasRepository->getById($id);
        return response()->json($registro);
    }

    public function update(AulasRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->aulasRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Aulas atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->aulasRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Aulas excluído com sucesso!']);
    }
}
