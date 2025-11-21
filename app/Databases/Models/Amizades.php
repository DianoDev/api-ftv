<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Amizades extends Model
{
    protected $primaryKey = "id";
    protected $table = 'amizades';
    public string $sequence = 'amizades_id_seq';
    protected $guarded = [];

    protected $casts = [
        'aceito_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status possíveis
    const STATUS_PENDENTE = 'pendente';
    const STATUS_ACEITO = 'aceito';
    const STATUS_RECUSADO = 'recusado';
    const STATUS_BLOQUEADO = 'bloqueado';

    /**
     * Relacionamento com o usuário que enviou a solicitação
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'usuario_id');
    }

    /**
     * Relacionamento com o usuário que recebeu a solicitação
     */
    public function amigo(): BelongsTo
    {

        return $this->belongsTo(Users::class, 'amigo_id');
    }

    /**
     * Scope para buscar apenas amizades aceitas
     */
    public function scopeAceitas($query)
    {
        return $query->where('status', self::STATUS_ACEITO);
    }

    /**
     * Scope para buscar apenas amizades pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', self::STATUS_PENDENTE);
    }

    /**
     * Verifica se a amizade está aceita
     */
    public function isAceita(): bool
    {
        return $this->status === self::STATUS_ACEITO;
    }

    /**
     * Verifica se a amizade está pendente
     */
    public function isPendente(): bool
    {
        return $this->status === self::STATUS_PENDENTE;
    }

    /**
     * Retorna o amigo (a outra pessoa na relação, não o usuário atual)
     */
    public function getAmigoInfoAttribute(): ?Users
    {
        $userId = Auth::id();

        if ($this->usuario_id == $userId) {
            return $this->amigo;
        }

        return $this->usuario;
    }
}
