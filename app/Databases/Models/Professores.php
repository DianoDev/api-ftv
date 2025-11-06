<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Professores extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'professores';
    public string $sequence = 'professores_id_seq';
    protected $guarded = [];
}
