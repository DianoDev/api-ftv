<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'cpf',
        'registro_profissional',
        'data_nascimento',
        'cidade',
        'estado',
        'especialidades',
        'anos_experiencia',
        'preco_hora_aula',
        'bio',
        'certificacoes',
        'disponivel',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'especialidades' => 'array',
            'certificacoes' => 'array',
            'disponivel' => 'boolean',
            'preco_hora_aula' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the table associated with the model.
     *
     * @var string
     */
    protected $table = 'professores';
}
