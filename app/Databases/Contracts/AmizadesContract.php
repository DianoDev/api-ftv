<?php

namespace App\Databases\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AmizadesContract
{
    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator;
    public function getAll();
    public function getById(int $id): Model;
    public function create(array $params, bool $autoCommit = true): bool;
    public function update(int $id, array $params, bool $autoCommit = true): bool;
    public function destroy(int $id, bool $autoCommit = true): bool;

    // Métodos específicos para amizades
    public function enviarSolicitacao(int $usuarioId, int $amigoId): bool;
    public function aceitarSolicitacao(int $amizadeId, int $usuarioId): bool;
    public function recusarSolicitacao(int $amizadeId, int $usuarioId): bool;
    public function bloquearUsuario(int $usuarioId, int $bloqueadoId): bool;
    public function desbloquearUsuario(int $usuarioId, int $bloqueadoId): bool;
    public function verificarAmizade(int $usuarioId, int $amigoId): ?Model;
    public function saoAmigos(int $usuarioId, int $amigoId): bool;
    public function getAmigos(int $usuarioId): Collection;
    public function getSolicitacoesPendentes(int $usuarioId): Collection;
    public function getSolicitacoesEnviadas(int $usuarioId): Collection;
    public function getUsuariosBloqueados(int $usuarioId): Collection;
    public function removerAmizade(int $usuarioId, int $amigoId): bool;
}
