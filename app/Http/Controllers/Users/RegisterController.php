<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\RegisterContract;
use App\Http\Requests\UsersRequest;


class RegisterController extends Controller
{
    public function __construct(private readonly RegisterContract $usersRepository)
    {
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
}
