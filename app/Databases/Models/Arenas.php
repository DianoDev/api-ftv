<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arenas extends Model
{
    protected $primaryKey = "id";
    protected $table = 'arenas';
    public string $sequence = 'arenas_id_seq';
    protected $guarded = [];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'fotos' => 'array',
        'horario_funcionamento' => 'array',
        'comodidades' => 'array',
        'rating' => 'decimal:2',
        'total_avaliacoes' => 'integer',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com User (proprietário)
     */
    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'proprietario_id');
    }

    /**
     * Relacionamento com Solicitações de Racha
     */
    public function solicitacoesRacha(): HasMany
    {
        return $this->hasMany(SolicitacoesRacha::class, 'arena_id');
    }
}
