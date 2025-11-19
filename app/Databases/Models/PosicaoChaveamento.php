<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;

class PosicaoChaveamento extends Model
{
    protected $table = 'posicoes_chaveamento';
    protected $guarded = [];

    protected $casts = [
        'posicao' => 'integer',
        'ordem_exibicao' => 'integer',
        'seed_inscricao1' => 'integer',
        'seed_inscricao2' => 'integer',
    ];

    /**
     * Relacionamento com o chaveamento
     */
    public function chaveamento()
    {
        return $this->belongsTo(ChaveamentoCampeonato::class, 'chaveamento_id');
    }

    /**
     * Relacionamento com a partida
     */
    public function partida()
    {
        return $this->belongsTo(PartidaCampeonato::class, 'partida_id');
    }

    /**
     * Relacionamento com a origem da inscrição 1
     */
    public function origemInscricao1Posicao()
    {
        return $this->belongsTo(PosicaoChaveamento::class, 'origem_inscricao1_posicao_id');
    }

    /**
     * Relacionamento com a origem da inscrição 2
     */
    public function origemInscricao2Posicao()
    {
        return $this->belongsTo(PosicaoChaveamento::class, 'origem_inscricao2_posicao_id');
    }

    /**
     * Relacionamento com inscrição 1
     */
    public function inscricao1()
    {
        return $this->belongsTo(InscricaoCampeonato::class, 'inscricao1_id');
    }

    /**
     * Relacionamento com inscrição 2
     */
    public function inscricao2()
    {
        return $this->belongsTo(InscricaoCampeonato::class, 'inscricao2_id');
    }

    /**
     * Relacionamento com inscrição vencedora
     */
    public function inscricaoVencedora()
    {
        return $this->belongsTo(InscricaoCampeonato::class, 'inscricao_vencedora_id');
    }

    /**
     * Relacionamento com próxima posição do vencedor
     */
    public function proximaPosicaoVencedor()
    {
        return $this->belongsTo(PosicaoChaveamento::class, 'proxima_posicao_vencedor_id');
    }

    /**
     * Relacionamento com próxima posição do perdedor
     */
    public function proximaPosicaoPerdedor()
    {
        return $this->belongsTo(PosicaoChaveamento::class, 'proxima_posicao_perdedor_id');
    }

    /**
     * Verifica se a posição está aguardando competidores
     */
    public function estaAguardando(): bool
    {
        return $this->status === 'aguardando';
    }

    /**
     * Verifica se a posição está pronta para jogar
     */
    public function estaPronta(): bool
    {
        return $this->status === 'pronta';
    }

    /**
     * Verifica se a partida está em andamento
     */
    public function estaEmAndamento(): bool
    {
        return $this->status === 'em_andamento';
    }

    /**
     * Verifica se a posição foi finalizada
     */
    public function foiFinalizada(): bool
    {
        return $this->status === 'finalizada';
    }

    /**
     * Verifica se a posição tem ambos os competidores definidos
     */
    public function temAmbosCompetidores(): bool
    {
        return $this->inscricao1_id !== null && $this->inscricao2_id !== null;
    }
}
