<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitacoesRacha extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'solicitacoes_racha';
    public string $sequence = 'solicitacoes_racha_id_seq';
    protected $guarded = [];
}
