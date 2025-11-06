<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\RachasContract;
use App\Databases\Models\Rachas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class RachasRepository implements RachasContract
{
    public function __construct(private Rachas $rachas)
    {
    }

    public function getById(int $id): Model
    {
        return Rachas::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Rachas::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Rachas::query();

        if (isset($pagination['quadra_id'])) {
            $keyword = mb_strtolower($pagination['quadra_id']);
            $query->whereRaw('lower(quadra_id) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'quadra_id', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $rachas = new Rachas([
                'quadra_id' => $params['quadra_id']
            ]);
            $rachas->save();

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
            $rachas = $this->getById($id);
            $rachas->update($params);

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
            $rachas = $this->getById($id);
            $rachas->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
