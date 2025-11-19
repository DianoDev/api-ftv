<?php

namespace App\Databases\Contracts;

use App\Databases\Models\ChaveamentoCampeonato;
use App\Databases\Models\PosicaoChaveamento;
use Illuminate\Database\Eloquent\Collection;

interface ChaveamentoContract
{
    /**
     * Buscar chaveamento por ID
     *
     * @param int $id
     * @return ChaveamentoCampeonato|null
     */
    public function buscarPorId(int $id): ?ChaveamentoCampeonato;

    /**
     * Buscar chaveamento por categoria
     *
     * @param int $categoriaId
     * @return ChaveamentoCampeonato|null
     */
    public function buscarPorCategoria(int $categoriaId): ?ChaveamentoCampeonato;

    /**
     * Criar chaveamento para uma categoria
     *
     * @param array $data
     * @return ChaveamentoCampeonato
     */
    public function criar(array $data): ChaveamentoCampeonato;

    /**
     * Atualizar chaveamento
     *
     * @param int $id
     * @param array $data
     * @return ChaveamentoCampeonato
     */
    public function atualizar(int $id, array $data): ChaveamentoCampeonato;

    /**
     * Gerar chaveamento automático para eliminação simples
     *
     * @param int $categoriaId
     * @param array $inscricoes Array de IDs das inscrições
     * @return ChaveamentoCampeonato
     */
    public function gerarEliminacaoSimples(int $categoriaId, array $inscricoes): ChaveamentoCampeonato;

    /**
     * Buscar posições do chaveamento por fase
     *
     * @param int $chaveamentoId
     * @param string $fase
     * @return Collection
     */
    public function buscarPosicoesPorFase(int $chaveamentoId, string $fase): Collection;

    /**
     * Atualizar resultado de uma posição e avançar vencedor
     *
     * @param int $posicaoId
     * @param int $inscricaoVencedoraId
     * @return PosicaoChaveamento
     */
    public function registrarResultado(int $posicaoId, int $inscricaoVencedoraId): PosicaoChaveamento;

    /**
     * Verificar se chaveamento está completo
     *
     * @param int $chaveamentoId
     * @return bool
     */
    public function estaCompleto(int $chaveamentoId): bool;

    /**
     * Buscar vencedor do chaveamento
     *
     * @param int $chaveamentoId
     * @return int|null ID da inscrição vencedora
     */
    public function buscarVencedor(int $chaveamentoId): ?int;
}
