<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\ArenasContract;
use App\Databases\Models\Arenas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class ArenasRepository implements ArenasContract
{
    public function __construct(private Arenas $arenas)
    {
    }

    public function getById(int $id): Model
    {
        return Arenas::query()
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Arenas::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Arenas::query();

        if (isset($pagination['nome'])) {
            $keyword = mb_strtolower($pagination['nome']);
            $query->whereRaw('lower(nome) like ?', ["%{$keyword}%"]);
        }

        $query->orderBy($pagination['sort'] ?? 'nome', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $arenas = new Arenas([
                // Campos obrigatórios
                'proprietario_id' => $params['proprietario_id'],
                'nome' => $params['nome'],
                'endereco' => $params['endereco'],
                'cidade' => $params['cidade'],
                'estado' => strtoupper($params['estado']),

                // Campos opcionais
                'descricao' => $params['descricao'] ?? null,
                'cnpj' => $params['cnpj'] ?? null,
                'cep' => $params['cep'] ?? null,
                'latitude' => $params['latitude'] ?? null,
                'longitude' => $params['longitude'] ?? null,
                'telefone' => $params['telefone'] ?? null,
                'whatsapp' => $params['whatsapp'] ?? null,
                'fotos' => $params['fotos'] ?? null,
                'horario_funcionamento' => $params['horario_funcionamento'] ?? null,
                'comodidades' => $params['comodidades'] ?? null,

                // Campos com valores padrão
                'rating' => $params['rating'] ?? 0,
                'total_avaliacoes' => $params['total_avaliacoes'] ?? 0,
                'ativo' => $params['ativo'] ?? true,
            ]);
            $arenas->save();

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
            $arenas = $this->getById($id);
            $arenas->update($params);

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
            $arenas = $this->getById($id);
            $arenas->delete();
            $autoCommit && DB::commit();
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw new Exception($ex->getMessage());
        }

        return true;
    }
}
