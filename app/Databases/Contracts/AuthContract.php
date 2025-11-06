<?php

namespace App\Databases\Contracts;

use App\Databases\Models\Users;

interface AuthContract
{
    public function create(array $params, bool $autoCommit = true): bool;
    public function findByEmail(string $email): ?Users;

    public function findById(int $id): ?Users;
    public function update(int $id, array $params, bool $autoCommit = true): bool;
    public function delete(int $id, bool $autoCommit = true): bool;
    public function getAll();
    public function attemptLogin(string $email, string $password): ?Users;
}
