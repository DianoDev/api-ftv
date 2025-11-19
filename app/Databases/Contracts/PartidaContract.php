<?php

namespace App\Databases\Contracts;

use App\Databases\Models\PartidaCampeonato;
use Illuminate\Database\Eloquent\Collection;

interface PartidaContract
{
    /**
     * Buscar partida por ID
     *
     * @param int $id
     * @return PartidaCampeonato|null
     */
    public function buscarPorId(int $id): ?PartidaCampeonato;

    /**
     * Listar partidas por categoria
     *
     * @param int $categoriaId
     * @return Collection
     */
    public function listarPorCategoria(int $categoriaId): Collection;

    /**
     * Listar partidas por fase
     *
     * @param int $categoriaId
     * @param string $fase
     * @return Collection
     */
    public function listarPorFase(int $categoriaId, string $fase): Collection;

    /**
     * Listar partidas por inscrição
     *
     * @param int $inscricaoId
     * @return Collection
     */
    public function listarPorInscricao(int $inscricaoId): Collection;

    /**
     * Criar nova partida
     *
     * @param array $data
     * @return PartidaCampeonato
     */
    public function criar(array $data): PartidaCampeonato;

    /**
     * Atualizar partida
     *
     * @param int $id
     * @param array $data
     * @return PartidaCampeonato
     */
    public function atualizar(int $id, array $data): PartidaCampeonato;

    /**
     * Iniciar partida
     *
     * @param int $id
     * @return PartidaCampeonato
     */
    public function iniciar(int $id): PartidaCampeonato;

    /**
     * Finalizar partida
     *
     * @param int $id
     * @param int $inscricaoVencedoraId
     * @return PartidaCampeonato
     */
    public function finalizar(int $id, int $inscricaoVencedoraId): PartidaCampeonato;

    /**
     * Registrar W.O.
     *
     * @param int $id
     * @param int $inscricaoVencedoraId
     * @param string $motivo
     * @return PartidaCampeonato
     */
    public function registrarWO(int $id, int $inscricaoVencedoraId, string $motivo): PartidaCampeonato;

    /**
     * Atualizar placar da partida
     *
     * @param int $id
     * @param int $setsInscricao1
     * @param int $setsInscricao2
     * @return PartidaCampeonato
     */
    public function atualizarPlacar(int $id, int $setsInscricao1, int $setsInscricao2): PartidaCampeonato;

    /**
     * Deletar partida
     *
     * @param int $id
     * @return bool
     */
    public function deletar(int $id): bool;
}
