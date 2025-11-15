<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitacoesRacha extends Model
{
    protected $primaryKey = "id";
    protected $table = 'solicitacoes_racha';
    public string $sequence = 'solicitacoes_racha_id_seq';
    protected $guarded = [];

    protected $casts = [
        'data_jogo' => 'date',
        'duracao_horas' => 'decimal:1',
        'limite_participantes' => 'integer',
        'participantes_atuais' => 'integer',
        'valor_estimado' => 'decimal:2',
        'valor_por_pessoa' => 'decimal:2',
        'data_limite_confirmacao' => 'datetime',
    ];

    /**
     * Relacionamento com User (criador)
     */
    public function criador(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'criador_id');
    }

    /**
     * Relacionamento com Arena
     */
    public function arena(): BelongsTo
    {
        return $this->belongsTo(Arenas::class, 'arena_id');
    }

    /**
     * Relacionamento com Participantes
     */
    public function participantes(): HasMany
    {
        return $this->hasMany(ParticipantesSolicitacao::class, 'solicitacao_id');
    }
}
