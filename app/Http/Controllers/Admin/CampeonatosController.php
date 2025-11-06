<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\CampeonatosContract;
use App\Http\Requests\CampeonatosRequest;
use Inertia\Inertia;
use Inertia\Response;

class CampeonatosController extends Controller
{
    public function __construct(private readonly CampeonatosContract $campeonatosRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Campeonatos/CampeonatosIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->campeonatosRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'organizador_id' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(CampeonatosRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->campeonatosRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Campeonatos criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->campeonatosRepository->getById($id);
        return response()->json($registro);
    }

    public function update(CampeonatosRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->campeonatosRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Campeonatos atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->campeonatosRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Campeonatos excluído com sucesso!']);
    }
}
