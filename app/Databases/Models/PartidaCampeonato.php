<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;

class PartidaCampeonato extends Model
{
    protected $table = 'partidas_campeonato';
    protected $guarded = [];

    protected $casts = [
        'data_hora' => 'datetime',
        'iniciada_em' => 'datetime',
        'finalizada_em' => 'datetime',
        'duracao_minutos' => 'integer',
        'sets_inscricao1' => 'integer',
        'sets_inscricao2' => 'integer',
    ];

    /**
     * Relacionamento com a categoria
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaCampeonato::class, 'categoria_id');
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
     * Relacionamento com a quadra
     */
    public function quadra()
    {
        return $this->belongsTo(Quadras::class, 'quadra_id');
    }

    /**
     * Relacionamento com o grupo
     */
    public function grupo()
    {
        return $this->belongsTo(GrupoCampeonato::class, 'grupo_id');
    }

    /**
     * Relacionamento com posição do chaveamento
     */
    public function posicaoChaveamento()
    {
        return $this->belongsTo(PosicaoChaveamento::class, 'posicao_chaveamento_id');
    }

    /**
     * Relacionamento com inscrição vencedora
     */
    public function inscricaoVencedora()
    {
        return $this->belongsTo(InscricaoCampeonato::class, 'inscricao_vencedora_id');
    }

    /**
     * Relacionamento com o árbitro
     */
    public function arbitro()
    {
        return $this->belongsTo(Users::class, 'arbitro_id');
    }

    /**
     * Relacionamento com os sets da partida
     */
    public function sets()
    {
        return $this->hasMany(SetPartidaCampeonato::class, 'partida_id');
    }

    /**
     * Relacionamento com os pontos da partida
     */
    public function pontos()
    {
        return $this->hasMany(PontoPartidaCampeonato::class, 'partida_id');
    }

    /**
     * Verifica se a partida está agendada
     */
    public function estaAgendada(): bool
    {
        return $this->status === 'agendada';
    }

    /**
     * Verifica se a partida está em andamento
     */
    public function estaEmAndamento(): bool
    {
        return $this->status === 'em_andamento';
    }

    /**
     * Verifica se a partida foi finalizada
     */
    public function foiFinalizada(): bool
    {
        return $this->status === 'finalizada';
    }

    /**
     * Verifica se a partida foi W.O.
     */
    public function foiWO(): bool
    {
        return $this->status === 'wo';
    }
}
