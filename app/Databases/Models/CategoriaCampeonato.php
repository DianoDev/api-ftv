<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaCampeonato extends Model
{
    protected $primaryKey = "id";
    protected $table = 'categorias_campeonato';
    public string $sequence = 'categorias_campeonato_id_seq';
    protected $guarded = [];
}
