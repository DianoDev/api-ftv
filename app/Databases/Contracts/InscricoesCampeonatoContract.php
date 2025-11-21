<?php

namespace App\Databases\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface InscricoesCampeonatoContract
{
    /**
     * Listar inscrições com paginação
     */
    public function paginate(array $pagination = []): LengthAwarePaginator;

    /**
     * Obter inscrição por ID
     */
    public function getById(int $id): ?object;

    /**
     * Criar nova inscrição
     */
    public function create(array $data): object;

    /**
     * Atualizar inscrição
     */
    public function update(int $id, array $data): bool;

    /**
     * Deletar inscrição
     */
    public function delete(int $id): bool;

    /**
     * Listar inscrições de um usuário
     */
    public function getByUsuario(int $usuarioId, array $pagination = []): LengthAwarePaginator;

    /**
     * Listar inscrições de uma categoria
     */
    public function getByCategoria(int $categoriaId, array $pagination = []): LengthAwarePaginator;

    /**
     * Verificar se usuário já está inscrito na categoria
     */
    public function usuarioJaInscrito(int $categoriaId, int $usuarioId): bool;

    /**
     * Confirmar pagamento da inscrição
     */
    public function confirmarPagamento(int $id): bool;

    /**
     * Atualizar status da inscrição
     */
    public function atualizarStatus(int $id, string $status): bool;
}
