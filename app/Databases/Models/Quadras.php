<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quadras extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'quadras';
    public string $sequence = 'quadras_id_seq';
    protected $guarded = [];
}
