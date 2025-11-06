<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    use SoftDeletes;

    protected $primaryKey = "id";
    protected $table = 'users';
    public string $sequence = 'users_id_seq';
    protected $guarded = [];
}
