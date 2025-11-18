<?php

namespace App\Databases\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Databases\Models\Post;

interface PostContract
{
    /**
     * Criar um novo post
     *
     * @param array $data
     * @return Post
     */
    public function criar(array $data): Post;

    /**
     * Buscar post por ID
     *
     * @param int $id
     * @return Post|null
     */
    public function buscarPorId(int $id): ?Post;

    /**
     * Listar posts válidos (ativos e não expirados)
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listarValidos(int $perPage = 10);

    /**
     * Listar posts de um usuário específico
     *
     * @param int $usuarioId
     * @param bool $apenasValidos
     * @return Collection
     */
    public function listarPorUsuario(int $usuarioId, bool $apenasValidos = true): Collection;

    /**
     * Listar posts de amigos de um usuário
     *
     * @param int $usuarioId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listarPostsAmigos(int $usuarioId, int $perPage = 10);

    /**
     * Deletar um post
     *
     * @param int $id
     * @return bool
     */
    public function deletar(int $id): bool;

    /**
     * Desativar posts expirados
     *
     * @return int Número de posts desativados
     */
    public function desativarExpirados(): int;

    /**
     * Verificar se usuário é dono do post
     *
     * @param int $postId
     * @param int $usuarioId
     * @return bool
     */
    public function usuarioDono(int $postId, int $usuarioId): bool;
}
