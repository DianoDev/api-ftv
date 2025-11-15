<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantesSolicitacao extends Model
{
    protected $primaryKey = "id";
    protected $table = 'participantes_solicitacao';
    public string $sequence = 'participantes_solicitacao_id_seq';
    protected $guarded = [];

    protected $casts = [
        'valor_pago' => 'decimal:2',
        'pagamento_confirmado' => 'boolean',
        'confirmado_em' => 'datetime',
    ];

    /**
     * Relacionamento com SolicitacoesRacha
     */
    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(SolicitacoesRacha::class, 'solicitacao_id');
    }

    /**
     * Relacionamento com User
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'usuario_id');
    }
}
