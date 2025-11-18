<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'conteudo',
        'imagem',
        'expira_em',
        'ativo',
    ];

    protected $casts = [
        'expira_em' => 'datetime',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Scope para posts ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para posts não expirados
     */
    public function scopeNaoExpirados($query)
    {
        return $query->where('expira_em', '>', Carbon::now());
    }

    /**
     * Scope para posts válidos (ativos e não expirados)
     */
    public function scopeValidos($query)
    {
        return $query->ativos()->naoExpirados();
    }

    /**
     * Verifica se o post está expirado
     */
    public function estaExpirado(): bool
    {
        return Carbon::now()->greaterThan($this->expira_em);
    }

    /**
     * Verifica se o post ainda é válido
     */
    public function estaValido(): bool
    {
        return $this->ativo && !$this->estaExpirado();
    }

    /**
     * Calcula o tempo restante até expiração em horas
     */
    public function horasRestantes(): float
    {
        if ($this->estaExpirado()) {
            return 0;
        }

        return Carbon::now()->diffInHours($this->expira_em, false);
    }
}
