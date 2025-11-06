<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\QuadrasContract;
use App\Http\Requests\QuadrasRequest;
use Inertia\Inertia;
use Inertia\Response;

class QuadrasController extends Controller
{
    public function __construct(private readonly QuadrasContract $quadrasRepository)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Quadras/QuadrasIndex');
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->quadrasRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'arena_id' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(QuadrasRequest $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->quadrasRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Quadras criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->quadrasRepository->getById($id);
        return response()->json($registro);
    }

    public function update(QuadrasRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->quadrasRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Quadras atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->quadrasRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Quadras excluído com sucesso!']);
    }
}
