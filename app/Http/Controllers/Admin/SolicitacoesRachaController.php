<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\SolicitacoesRachaContract;
use App\Http\Requests\SolicitacoesRachaRequest;
use Inertia\Inertia;
use Inertia\Response;

class SolicitacoesRachaController extends Controller
{
    public function __construct(private readonly SolicitacoesRachaContract $solicitacoesRachaRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/SolicitacoesRacha/SolicitacoesRachaIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->solicitacoesRachaRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'criador_id' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(SolicitacoesRachaRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->solicitacoesRachaRepository->create($params);
        return response()->json(['success' => true, 'message' => 'SolicitacoesRacha criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->solicitacoesRachaRepository->getById($id);
        return response()->json($registro);
    }

    public function update(SolicitacoesRachaRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->solicitacoesRachaRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'SolicitacoesRacha atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->solicitacoesRachaRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'SolicitacoesRacha excluído com sucesso!']);
    }
}
