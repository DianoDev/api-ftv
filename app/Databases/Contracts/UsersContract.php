<?php

namespace App\Databases\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface UsersContract
{
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator;
    public function getAll();
    public function getById(int $id): Model;
    public function create(array $params, bool $autoCommit = true): bool;
    public function update(int $id, array $params, bool $autoCommit = true): bool;
    public function destroy(int $id, bool $autoCommit = true): bool;
}
