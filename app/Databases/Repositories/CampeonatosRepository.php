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
            ->with('categoriasCampeonato.inscricaoCampeonato')
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

        // Filtro por organizador
        if (isset($pagination['organizador_id'])) {
            $query->where('organizador_id', '=', $pagination['organizador_id']);
        }

        // Filtro por nome
        if (isset($pagination['nome'])) {
            $keyword = mb_strtolower($pagination['nome']);
            $query->whereRaw('lower(nome) like ?', ["%{$keyword}%"]);
        }

        // Filtro por status
        if (isset($pagination['status'])) {
            $query->where('status', '=', $pagination['status']);
        }

        // Filtro por arena
        if (isset($pagination['arena_id'])) {
            $query->where('arena_id', '=', $pagination['arena_id']);
        }

        $query->orderBy($pagination['sort'] ?? 'data_inicio', $pagination['sort_direction'] ?? 'desc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $campeonatos = new Campeonatos([
                // Campos obrigatórios
                'organizador_id' => $params['organizador_id'],
                'nome' => $params['nome'],
                'data_inicio' => $params['data_inicio'],
                'data_fim' => $params['data_fim'],

                // Campos opcionais
                'arena_id' => $params['arena_id'] ?? null,
                'descricao' => $params['descricao'] ?? null,
                'tipo' => $params['tipo'] ?? null,
                'regras' => $params['regras'] ?? null,
                'foto_capa' => $params['foto_capa'] ?? null,

                // Campo com valor padrão
                'status' => $params['status'] ?? 'inscricoes_abertas',
            ]);
            $campeonatos->save();

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
            $campeonatos = $this->getById($id);

            // Atualiza apenas os campos fornecidos
            if (isset($params['nome'])) {
                $campeonatos->nome = $params['nome'];
            }
            if (isset($params['descricao'])) {
                $campeonatos->descricao = $params['descricao'];
            }
            if (isset($params['data_inicio'])) {
                $campeonatos->data_inicio = $params['data_inicio'];
            }
            if (isset($params['data_fim'])) {
                $campeonatos->data_fim = $params['data_fim'];
            }
            if (isset($params['tipo'])) {
                $campeonatos->tipo = $params['tipo'];
            }
            if (isset($params['regras'])) {
                $campeonatos->regras = $params['regras'];
            }
            if (isset($params['status'])) {
                $campeonatos->status = $params['status'];
            }
            if (isset($params['arena_id'])) {
                $campeonatos->arena_id = $params['arena_id'];
            }
            if (isset($params['foto_capa'])) {
                $campeonatos->foto_capa = $params['foto_capa'];
            }

            $campeonatos->save();

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
