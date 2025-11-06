<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rachas extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'rachas';
    public string $sequence = 'rachas_id_seq';
    protected $guarded = [];
}
