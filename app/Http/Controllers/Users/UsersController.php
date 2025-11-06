<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\UsersContract;
use App\Http\Requests\UsersRequest;


class UsersController extends Controller
{
    public function __construct(private readonly UsersContract $usersRepository)
    {
    }

    public function list(Request $request): JsonResponse
    {
        $dados = $this->usersRepository->paginate($request->all())->toArray();
        $dados['filter_options'] = [
            'nome' => [
                'type' => 'text',
            ]
        ];
        return response()->json($dados);
    }

    public function create(Request $request): JsonResponse
    {
        $params = $request->except('_token');
        $this->usersRepository->create($params);
        return response()->json(['success' => true, 'message' => 'Users criado com sucesso!']);
    }

    public function edit(int $id): JsonResponse
    {
        $registro = $this->usersRepository->getById($id);
        return response()->json($registro);
    }

    public function update(UsersRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $this->usersRepository->update($id, $params);
        return response()->json(['success' => true, 'message' => 'Users atualizado com sucesso!']);
    }

    public function delete(int $id): JsonResponse
    {
        $this->usersRepository->destroy($id);
        return response()->json(['success' => true, 'message' => 'Users excluído com sucesso!']);
    }
}
