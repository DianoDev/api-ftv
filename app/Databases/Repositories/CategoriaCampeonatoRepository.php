<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\CategoriaCampeonatoContract;
use App\Databases\Models\CategoriaCampeonato;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoriaCampeonatoRepository implements CategoriaCampeonatoContract
{
    public function __construct(private CategoriaCampeonato $CategoriaCampeonato)
    {
    }

    public function getById(int $id): Model
    {
        return CategoriaCampeonato::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return CategoriaCampeonato::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = CategoriaCampeonato::query();

        // Filtro por campeonato (essencial)
        if (isset($pagination['campeonato_id'])) {
            $query->where('campeonato_id', '=', $pagination['campeonato_id']);
        }

        // Filtro por nome
        if (isset($pagination['nome'])) {
            $keyword = mb_strtolower($pagination['nome']);
            $query->whereRaw('lower(nome) like ?', ["%{$keyword}%"]);
        }

        // Filtro por gênero
        if (isset($pagination['genero'])) {
            $query->where('genero', '=', $pagination['genero']);
        }

        // Filtro por nível
        if (isset($pagination['nivel'])) {
            $query->where('nivel', '=', $pagination['nivel']);
        }

        // Filtro por status
        if (isset($pagination['status'])) {
            $query->where('status', '=', $pagination['status']);
        }

        $query->orderBy($pagination['sort'] ?? 'nome', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $categoria = new CategoriaCampeonato([
                // Campos obrigatórios
                'campeonato_id' => $params['campeonato_id'],
                'nome' => $params['nome'],

                // Campos opcionais
                'genero' => $params['genero'] ?? null,
                'nivel' => $params['nivel'] ?? null,
                'max_duplas' => $params['max_duplas'] ?? null,
                'valor_inscricao' => $params['valor_inscricao'] ?? 0,
                'premiacao' => $params['premiacao'] ?? null,

                // Campo com valor padrão
                'status' => $params['status'] ?? 'inscricoes_abertas',
            ]);
            $categoria->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }
    }

    public function update(int $id, array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $categoria = $this->getById($id);

            // Atualiza apenas os campos fornecidos
            if (isset($params['nome'])) {
                $categoria->nome = $params['nome'];
            }
            if (isset($params['genero'])) {
                $categoria->genero = $params['genero'];
            }
            if (isset($params['nivel'])) {
                $categoria->nivel = $params['nivel'];
            }
            if (isset($params['max_duplas'])) {
                $categoria->max_duplas = $params['max_duplas'];
            }
            if (isset($params['valor_inscricao'])) {
                $categoria->valor_inscricao = $params['valor_inscricao'];
            }
            if (isset($params['premiacao'])) {
                $categoria->premiacao = $params['premiacao'];
            }
            if (isset($params['status'])) {
                $categoria->status = $params['status'];
            }

            $categoria->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }
    }

    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $categoria = $this->getById($id);
            $categoria->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
