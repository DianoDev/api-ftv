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

        // Adicionar contagem de participantes
        $query->withCount('participantes');

        // Carregar relacionamento com arena
        $query->with('arena:id,nome,cidade,estado');

        // Filtrar por criador
        if (isset($pagination['criador_id'])) {
            $query->where('criador_id', $pagination['criador_id']);
        }

        // Filtrar por arena
        if (isset($pagination['arena_id'])) {
            $query->where('arena_id', $pagination['arena_id']);
        }

        // Filtrar por status
        if (isset($pagination['status'])) {
            $query->where('status', $pagination['status']);
        }

        // Filtrar por data (futuras, passadas, etc)
        if (isset($pagination['data_jogo'])) {
            $query->whereDate('data_jogo', $pagination['data_jogo']);
        }

        // Filtrar apenas solicitações abertas e futuras
        if (isset($pagination['abertas']) && $pagination['abertas']) {
            $query->where('status', 'aberta')
                  ->where('data_jogo', '>=', now()->toDateString());
        }

        $query->orderBy($pagination['sort'] ?? 'data_jogo', $pagination['sort_direction'] ?? 'asc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            // Calcular duração em horas se não foi fornecida
            $duracao_horas = $params['duracao_horas'] ?? null;
            if (!$duracao_horas && isset($params['hora_inicio']) && isset($params['hora_fim'])) {
                $inicio = \Carbon\Carbon::createFromFormat('H:i', $params['hora_inicio']);
                $fim = \Carbon\Carbon::createFromFormat('H:i', $params['hora_fim']);
                $duracao_horas = $fim->diffInMinutes($inicio) / 60;
            }

            $solicitacoesRacha = new SolicitacoesRacha([
                'criador_id' => $params['criador_id'],
                'arena_id' => $params['arena_id'],
                'data_jogo' => $params['data_jogo'],
                'hora_inicio' => $params['hora_inicio'],
                'hora_fim' => $params['hora_fim'],
                'duracao_horas' => $duracao_horas,
                'limite_participantes' => $params['limite_participantes'],
                'participantes_atuais' => 1, // Criador é o primeiro participante
                'valor_estimado' => $params['valor_estimado'] ?? null,
                'valor_por_pessoa' => $params['valor_por_pessoa'] ?? null,
                'status' => 'aberta',
                'nivel_sugerido' => $params['nivel_sugerido'] ?? null,
                'descricao' => $params['descricao'] ?? null,
                'observacoes' => $params['observacoes'] ?? null,
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
