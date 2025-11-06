<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\ProfessoresContract;
use App\Databases\Models\Professores;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class ProfessoresRepository implements ProfessoresContract
{
    public function __construct(private Professores $professores)
    {
    }

    public function getById(int $id): Model
    {
        return Professores::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Professores::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Professores::query();

        if (isset($pagination['user_id'])) {
            $keyword = mb_strtolower($pagination['user_id']);
            $query->whereRaw('lower(user_id) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'user_id', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $professores = new Professores([
                'user_id' => $params['user_id']
            ]);
            $professores->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    public function update(int $id, array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $professores = $this->getById($id);
            $professores->update($params);

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex);
        }
    }

    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $professores = $this->getById($id);
            $professores->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
