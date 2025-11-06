<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\SolicitacoesRachaContract;
use App\Databases\Models\SolicitacoesRacha;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class SolicitacoesRachaRepository implements SolicitacoesRachaContract
{
    public function __construct(private SolicitacoesRacha $solicitacoesRacha)
    {
    }

    public function getById(int $id): Model
    {
        return SolicitacoesRacha::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return SolicitacoesRacha::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = SolicitacoesRacha::query();

        if (isset($pagination['criador_id'])) {
            $keyword = mb_strtolower($pagination['criador_id']);
            $query->whereRaw('lower(criador_id) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'criador_id', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $solicitacoesRacha = new SolicitacoesRacha([
                'criador_id' => $params['criador_id']
            ]);
            $solicitacoesRacha->save();

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
            $solicitacoesRacha = $this->getById($id);
            $solicitacoesRacha->update($params);

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
            $solicitacoesRacha = $this->getById($id);
            $solicitacoesRacha->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
