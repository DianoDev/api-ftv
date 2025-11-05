<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arena extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nome_estabelecimento',
        'cnpj',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'latitude',
        'longitude',
        'numero_quadras',
        'comodidades',
        'horario_abertura',
        'horario_fechamento',
        'dias_funcionamento',
        'preco_hora_quadra',
        'fotos',
        'descricao',
        'ativa',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'comodidades' => 'array',
            'dias_funcionamento' => 'array',
            'fotos' => 'array',
            'ativa' => 'boolean',
            'preco_hora_quadra' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
