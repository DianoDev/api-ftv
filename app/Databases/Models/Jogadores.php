<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jogadores extends Model
{
    protected $primaryKey = "id";
    protected $table = 'jogadores';
    public string $sequence = 'jogadores_id_seq';
    protected $guarded = [];

    protected $casts = [
        'ranking' => 'integer',
        'total_rachas' => 'integer',
        'vitorias' => 'integer',
        'derrotas' => 'integer',
    ];

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
