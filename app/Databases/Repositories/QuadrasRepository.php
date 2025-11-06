<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\QuadrasContract;
use App\Databases\Models\Quadras;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class QuadrasRepository implements QuadrasContract
{
    public function __construct(private Quadras $quadras)
    {
    }

    public function getById(int $id): Model
    {
        return Quadras::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Quadras::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Quadras::query();

        if (isset($pagination['arena_id'])) {
            $keyword = mb_strtolower($pagination['arena_id']);
            $query->whereRaw('lower(arena_id) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'arena_id', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $quadras = new Quadras([
                'arena_id' => $params['arena_id']
            ]);
            $quadras->save();

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
            $quadras = $this->getById($id);
            $quadras->update($params);

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
            $quadras = $this->getById($id);
            $quadras->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
