<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aulas extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'aulas';
    public string $sequence = 'aulas_id_seq';
    protected $guarded = [];
}
