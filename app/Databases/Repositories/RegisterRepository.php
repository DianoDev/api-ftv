<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\RegisterContract;
use App\Databases\Models\Users;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegisterRepository implements RegisterContract
{
    public function __construct(private Users $users)
    {
    }

    public function getById(int $id): Model
    {
        return Users::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Users::query()->get();
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $users = new Users([
                'nome' => $params['nome'],
                'email' => $params['email'],
                'tipo_usuario' => $params['tipo_usuario'],
                'password' => Hash::make($params['password'])
            ]);
            $users->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            // Melhor tratamento de erro
            throw new Exception('Erro ao criar usuário: ' . $ex->getMessage());
        }
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?Users
    {
        return Users::where('email', $email)->first();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?Users
    {
        return Users::find($id);
    }

    /**
     * Update user
     */
    public function update(int $id, array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $users = Users::findOrFail($id);

            if (isset($params['nome'])) {
                $users->nome = $params['nome'];
            }
            if (isset($params['email'])) {
                $users->email = $params['email'];
            }
            if (isset($params['tipo_usuario'])) {
                $users->tipo_usuario = $params['tipo_usuario'];
            }
            if (isset($params['password'])) {
                $users->password = Hash::make($params['password']);
            }

            $users->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception('Erro ao atualizar usuário: ' . $ex->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function delete(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $users = Users::findOrFail($id);
            $users->delete();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception('Erro ao deletar usuário: ' . $ex->getMessage());
        }
    }

}
