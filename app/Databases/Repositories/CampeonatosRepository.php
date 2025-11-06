<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\CampeonatosContract;
use App\Databases\Models\Campeonatos;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class CampeonatosRepository implements CampeonatosContract
{
    public function __construct(private Campeonatos $campeonatos)
    {
    }

    public function getById(int $id): Model
    {
        return Campeonatos::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Campeonatos::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Campeonatos::query();

        if (isset($pagination['organizador_id'])) {
            $keyword = mb_strtolower($pagination['organizador_id']);
            $query->whereRaw('lower(organizador_id) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'organizador_id', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $campeonatos = new Campeonatos([
                'organizador_id' => $params['organizador_id']
            ]);
            $campeonatos->save();

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
            $campeonatos = $this->getById($id);
            $campeonatos->update($params);

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
            $campeonatos = $this->getById($id);
            $campeonatos->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
