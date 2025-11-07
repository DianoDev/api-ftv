<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jogadores extends Model
{
    protected $primaryKey = "id";
    protected $table = 'jogadores';
    public string $sequence = 'jogadores_id_seq';
    protected $guarded = [];
}
