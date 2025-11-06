<?php

namespace App\Databases\Repositories;

use App\Databases\Contracts\AuthContract;
use App\Databases\Models\Users;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthContract
{
    /**
     * Create a new user
     */
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

    /**
     * Get all users
     */
    public function getAll()
    {
        return Users::select('id', 'nome', 'email', 'tipo_usuario', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Attempt to authenticate a user
     *
     * @param string $email
     * @param string $password
     * @return Users|null
     */
    public function attemptLogin(string $email, string $password): ?Users
    {
        // Busca o usuário pelo email
        $user = $this->findByEmail($email);

        // Se o usuário não existir, retorna null
        if (!$user) {
            return null;
        }

        // Verifica se a senha está correta
        if (!Hash::check($password, $user->password)) {
            return null;
        }

        // Retorna o usuário autenticado
        return $user;
    }
}
