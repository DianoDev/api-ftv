<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\AulasContract;
use App\Databases\Models\Aulas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class AulasRepository implements AulasContract
{
    public function __construct(private Aulas $aulas)
    {
    }

    public function getById(int $id): Model
    {
        return Aulas::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Aulas::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Aulas::query();

        if (isset($pagination['data_aula'])) {
            $keyword = mb_strtolower($pagination['data_aula']);
            $query->whereRaw('lower(data_aula) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'data_aula', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $aulas = new Aulas([
                'data_aula' => $params['data_aula']
            ]);
            $aulas->save();

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
            $aulas = $this->getById($id);
            $aulas->update($params);

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
            $aulas = $this->getById($id);
            $aulas->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
