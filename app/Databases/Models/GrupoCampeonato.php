<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoCampeonato extends Model
{
    protected $table = 'grupos_campeonato';
    protected $guarded = [];

    /**
     * Relacionamento com a categoria
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaCampeonato::class, 'categoria_id');
    }

    /**
     * Relacionamento com as inscrições do grupo
     */
    public function inscricoes()
    {
        return $this->belongsToMany(
            InscricaoCampeonato::class,
            'inscricoes_grupo',
            'grupo_id',
            'inscricao_id'
        );
    }

    /**
     * Relacionamento com as partidas do grupo
     */
    public function partidas()
    {
        return $this->hasMany(PartidaCampeonato::class, 'grupo_id');
    }
}
