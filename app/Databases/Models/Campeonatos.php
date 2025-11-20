<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campeonatos extends Model
{
    protected $primaryKey = "id";
    protected $table = 'campeonatos';
    public string $sequence = 'campeonatos_id_seq';
    protected $guarded = [];


    public function categoriasCampeonato(): HasMany
    {
        return $this->hasMany(CategoriaCampeonato::class, 'campeonato_id');
    }

}
