<?php

namespace App\Databases\Repositories;

use App\Databases\Contracts\PartidaContract;
use App\Databases\Models\PartidaCampeonato;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PartidaRepository implements PartidaContract
{
    protected $model;

    public function __construct(PartidaCampeonato $model)
    {
        $this->model = $model;
    }

    public function buscarPorId(int $id): ?PartidaCampeonato
    {
        return $this->model
            ->with([
                'categoria',
                'inscricao1.usuario',
                'inscricao1.parceiro',
                'inscricao2.usuario',
                'inscricao2.parceiro',
                'quadra',
                'inscricaoVencedora.usuario',
                'arbitro',
                'sets',
                'pontos'
            ])
            ->find($id);
    }

    public function listarPorCategoria(int $categoriaId): Collection
    {
        return $this->model
            ->with([
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'inscricaoVencedora.usuario'
            ])
            ->where('categoria_id', $categoriaId)
            ->orderBy('data_hora', 'asc')
            ->get();
    }

    public function listarPorFase(int $categoriaId, string $fase): Collection
    {
        return $this->model
            ->with([
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'inscricaoVencedora.usuario'
            ])
            ->where('categoria_id', $categoriaId)
            ->where('fase', $fase)
            ->orderBy('data_hora', 'asc')
            ->get();
    }

    public function listarPorInscricao(int $inscricaoId): Collection
    {
        return $this->model
            ->with([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'inscricaoVencedora.usuario',
                'sets'
            ])
            ->where(function ($query) use ($inscricaoId) {
                $query->where('inscricao1_id', $inscricaoId)
                    ->orWhere('inscricao2_id', $inscricaoId);
            })
            ->orderBy('data_hora', 'desc')
            ->get();
    }

    public function criar(array $data): PartidaCampeonato
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->create($data);

            DB::commit();

            return $partida->load([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function atualizar(int $id, array $data): PartidaCampeonato
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->findOrFail($id);
            $partida->update($data);

            DB::commit();

            return $partida->load([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'inscricaoVencedora.usuario'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function iniciar(int $id): PartidaCampeonato
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->findOrFail($id);

            if ($partida->estaEmAndamento() || $partida->foiFinalizada()) {
                throw new \Exception('Partida já foi iniciada ou finalizada');
            }

            $partida->update([
                'status' => 'em_andamento',
                'iniciada_em' => now(),
            ]);

            // Atualizar posição do chaveamento se existir
            if ($partida->posicao_chaveamento_id) {
                $partida->posicaoChaveamento()->update([
                    'status' => 'em_andamento',
                ]);
            }

            DB::commit();

            return $partida->load([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function finalizar(int $id, int $inscricaoVencedoraId): PartidaCampeonato
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->findOrFail($id);

            if (!$partida->estaEmAndamento()) {
                throw new \Exception('Partida não está em andamento');
            }

            // Validar que o vencedor é um dos competidores
            if ($partida->inscricao1_id !== $inscricaoVencedoraId && $partida->inscricao2_id !== $inscricaoVencedoraId) {
                throw new \Exception('Vencedor inválido para esta partida');
            }

            $partida->update([
                'status' => 'finalizada',
                'inscricao_vencedora_id' => $inscricaoVencedoraId,
                'finalizada_em' => now(),
            ]);

            DB::commit();

            return $partida->load([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'inscricaoVencedora.usuario',
                'sets'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function registrarWO(int $id, int $inscricaoVencedoraId, string $motivo): PartidaCampeonato
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->findOrFail($id);

            // Validar que o vencedor é um dos competidores
            if ($partida->inscricao1_id !== $inscricaoVencedoraId && $partida->inscricao2_id !== $inscricaoVencedoraId) {
                throw new \Exception('Vencedor inválido para esta partida');
            }

            $partida->update([
                'status' => 'wo',
                'inscricao_vencedora_id' => $inscricaoVencedoraId,
                'observacoes' => 'W.O. - ' . $motivo,
                'finalizada_em' => now(),
            ]);

            DB::commit();

            return $partida->load([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'inscricaoVencedora.usuario'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function atualizarPlacar(int $id, int $setsInscricao1, int $setsInscricao2): PartidaCampeonato
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->findOrFail($id);

            $partida->update([
                'sets_inscricao1' => $setsInscricao1,
                'sets_inscricao2' => $setsInscricao2,
            ]);

            DB::commit();

            return $partida->load([
                'categoria',
                'inscricao1.usuario',
                'inscricao2.usuario',
                'quadra',
                'sets'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deletar(int $id): bool
    {
        DB::beginTransaction();

        try {
            $partida = $this->model->findOrFail($id);

            if ($partida->estaEmAndamento() || $partida->foiFinalizada()) {
                throw new \Exception('Não é possível deletar uma partida em andamento ou finalizada');
            }

            $deleted = $partida->delete();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
