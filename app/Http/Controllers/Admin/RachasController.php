<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\RachasContract;
use App\Http\Requests\RachasRequest;
use Inertia\Inertia;
use Inertia\Response;

class RachasController extends Controller
{
    public function __construct(private readonly RachasContract $rachasRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Rachas/RachasIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->rachasRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'quadra_id' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(RachasRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->rachasRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Rachas criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->rachasRepository->getById($id);
        return response()->json($registro);
    }

    public function update(RachasRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->rachasRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Rachas atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->rachasRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Rachas excluído com sucesso!']);
    }
}
