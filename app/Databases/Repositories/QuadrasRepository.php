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

        // Filtro por arena
        if (isset($pagination['arena_id'])) {
            $query->where('arena_id', '=', $pagination['arena_id']);
        }

        // Filtro por nome
        if (isset($pagination['nome'])) {
            $keyword = mb_strtolower($pagination['nome']);
            $query->whereRaw('lower(nome) like ?', ["%{$keyword}%"]);
        }

        // Filtro por ativa
        if (isset($pagination['ativa'])) {
            $query->where('ativa', '=', $pagination['ativa']);
        }

        // Filtro por coberta
        if (isset($pagination['coberta'])) {
            $query->where('coberta', '=', $pagination['coberta']);
        }

        $query->orderBy($pagination['sort'] ?? 'nome', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $quadras = new Quadras([
                // Campos obrigatórios
                'arena_id' => $params['arena_id'],
                'nome' => $params['nome'],

                // Campos opcionais de dimensão
                'comprimento' => $params['comprimento'] ?? null,
                'largura' => $params['largura'] ?? null,
                'valor_hora' => $params['valor_hora'] ?? null,

                // Campos booleanos com valores padrão
                'coberta' => $params['coberta'] ?? false,
                'iluminacao' => $params['iluminacao'] ?? true,
                'ativa' => $params['ativa'] ?? true,

                // Campo de observações
                'observacoes' => $params['observacoes'] ?? null,
            ]);
            $quadras->save();

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
            $quadras = $this->getById($id);

            // Atualiza apenas os campos fornecidos
            if (isset($params['nome'])) {
                $quadras->nome = $params['nome'];
            }
            if (isset($params['comprimento'])) {
                $quadras->comprimento = $params['comprimento'];
            }
            if (isset($params['largura'])) {
                $quadras->largura = $params['largura'];
            }
            if (isset($params['valor_hora'])) {
                $quadras->valor_hora = $params['valor_hora'];
            }
            if (isset($params['coberta'])) {
                $quadras->coberta = $params['coberta'];
            }
            if (isset($params['iluminacao'])) {
                $quadras->iluminacao = $params['iluminacao'];
            }
            if (isset($params['ativa'])) {
                $quadras->ativa = $params['ativa'];
            }
            if (isset($params['observacoes'])) {
                $quadras->observacoes = $params['observacoes'];
            }

            $quadras->save();

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
