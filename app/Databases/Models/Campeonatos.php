<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campeonatos extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'campeonatos';
    public string $sequence = 'campeonatos_id_seq';
    protected $guarded = [];
}
