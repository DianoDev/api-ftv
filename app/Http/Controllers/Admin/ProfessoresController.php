<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\ProfessoresContract;
use App\Http\Requests\ProfessoresRequest;
use Inertia\Inertia;
use Inertia\Response;

class ProfessoresController extends Controller
{
    public function __construct(private readonly ProfessoresContract $professoresRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Professores/ProfessoresIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->professoresRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'user_id' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(ProfessoresRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->professoresRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Professores criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->professoresRepository->getById($id);
        return response()->json($registro);
    }

    public function update(ProfessoresRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->professoresRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Professores atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->professoresRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Professores excluído com sucesso!']);
    }
}
