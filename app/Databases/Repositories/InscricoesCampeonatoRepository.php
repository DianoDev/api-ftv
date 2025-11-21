<?php

namespace App\Databases\Repositories;

use App\Databases\Contracts\InscricoesCampeonatoContract;
use App\Databases\Models\InscricaoCampeonato;
use Illuminate\Pagination\LengthAwarePaginator;

class InscricoesCampeonatoRepository implements InscricoesCampeonatoContract
{
    public function __construct(private readonly InscricaoCampeonato $model)
    {
    }

    public function paginate(array $pagination = []): LengthAwarePaginator
    {
        $query = $this->model->with(['categoria.campeonato', 'usuario', 'parceiro']);

        // Filtros
        if (!empty($pagination['categoria_id'])) {
            $query->where('categoria_id', $pagination['categoria_id']);
        }

        if (!empty($pagination['usuario_id'])) {
            $query->where(function ($q) use ($pagination) {
                $q->where('jogador1_id', $pagination['usuario_id'])
                  ->orWhere('jogador2_id', $pagination['usuario_id'])
                  ->orWhere('jogador3_id', $pagination['usuario_id'])
                  ->orWhere('jogador4_id', $pagination['usuario_id']);
            });
        }

        if (!empty($pagination['status'])) {
            $query->where('status', $pagination['status']);
        }

        $perPage = $pagination['per_page'] ?? 10;
        $page = $pagination['page'] ?? 1;

        return $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(int $id): ?object
    {
        return $this->model
            ->with(['categoria.campeonato', 'usuario', 'parceiro'])
            ->find($id);
    }

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $inscricao = $this->model->find($id);
        if (!$inscricao) {
            return false;
        }
        return $inscricao->update($data);
    }

    public function delete(int $id): bool
    {
        $inscricao = $this->model->find($id);
        if (!$inscricao) {
            return false;
        }
        return $inscricao->delete();
    }

    public function getByUsuario(int $usuarioId, array $pagination = []): LengthAwarePaginator
    {
        $pagination['usuario_id'] = $usuarioId;
        return $this->paginate($pagination);
    }

    public function getByCategoria(int $categoriaId, array $pagination = []): LengthAwarePaginator
    {
        $pagination['categoria_id'] = $categoriaId;
        return $this->paginate($pagination);
    }

    public function usuarioJaInscrito(int $categoriaId, int $usuarioId): bool
    {
        return $this->model
            ->where('categoria_id', $categoriaId)
            ->where(function ($query) use ($usuarioId) {
                $query->where('jogador1_id', $usuarioId)
                      ->orWhere('jogador2_id', $usuarioId)
                      ->orWhere('jogador3_id', $usuarioId)
                      ->orWhere('jogador4_id', $usuarioId);
            })
            ->exists();
    }

    public function confirmarPagamento(int $id): bool
    {
        return $this->update($id, ['pagamento_confirmado' => true]);
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }
}
