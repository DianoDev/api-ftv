<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arenas extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'arenas';
    public string $sequence = 'arenas_id_seq';
    protected $guarded = [];
}
