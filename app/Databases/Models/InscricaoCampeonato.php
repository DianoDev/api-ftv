<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;

class InscricaoCampeonato extends Model
{
    protected $table = 'inscricoes_campeonato';
    protected $guarded = [];

    /**
     * Relacionamento com a categoria
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaCampeonato::class, 'categoria_id');
    }

    /**
     * Relacionamento com usuário/jogador
     */
    public function usuario()
    {
        return $this->belongsTo(Users::class, 'usuario_id');
    }

    /**
     * Relacionamento com dupla (se for categoria de duplas)
     */
    public function parceiro()
    {
        return $this->belongsTo(Users::class, 'parceiro_id');
    }

    /**
     * Partidas como inscricao1
     */
    public function partidasComoInscricao1()
    {
        return $this->hasMany(PartidaCampeonato::class, 'inscricao1_id');
    }

    /**
     * Partidas como inscricao2
     */
    public function partidasComoInscricao2()
    {
        return $this->hasMany(PartidaCampeonato::class, 'inscricao2_id');
    }

    /**
     * Todas as partidas
     */
    public function partidas()
    {
        return PartidaCampeonato::where('inscricao1_id', $this->id)
            ->orWhere('inscricao2_id', $this->id)
            ->get();
    }
}
