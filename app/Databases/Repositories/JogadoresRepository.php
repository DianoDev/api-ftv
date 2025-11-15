<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\JogadoresContract;
use App\Databases\Models\Jogadores;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class JogadoresRepository implements JogadoresContract
{
    public function __construct(private Jogadores $jogadores)
    {
    }

    public function getById(int $id): Model
    {
        return Jogadores::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Jogadores::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Jogadores::query();

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
            $jogadores = new Jogadores([
                'user_id' => $params['user_id'],
                'nivel' => $params['nivel'] ?? 'iniciante',
                'lado_preferido' => $params['lado_preferido'] ?? 'ambos',
                'ranking' => $params['ranking'] ?? 1000,
                'total_rachas' => $params['total_rachas'] ?? 0,
                'vitorias' => $params['vitorias'] ?? 0,
                'derrotas' => $params['derrotas'] ?? 0,
                'posicao_preferida' => $params['posicao_preferida'] ?? null,
                'nivel_jogo' => $params['nivel_jogo'] ?? null,
            ]);
            $jogadores->save();

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
            $jogadores = $this->getById($id);
            $jogadores->update($params);

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
            $jogadores = $this->getById($id);
            $jogadores->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
