<?php

namespace App\Databases\Models;

use Illuminate\Database\Eloquent\Model;

class ChaveamentoCampeonato extends Model
{
    protected $table = 'chaveamento_campeonato';
    protected $guarded = [];

    protected $casts = [
        'total_participantes' => 'integer',
        'disputa_terceiro_lugar' => 'boolean',
        'chaveamento_gerado' => 'boolean',
        'gerado_em' => 'datetime',
    ];

    /**
     * Relacionamento com a categoria
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaCampeonato::class, 'categoria_id');
    }

    /**
     * Relacionamento com as posições do chaveamento
     */
    public function posicoes()
    {
        return $this->hasMany(PosicaoChaveamento::class, 'chaveamento_id');
    }

    /**
     * Verifica se o chaveamento já foi gerado
     */
    public function foiGerado(): bool
    {
        return $this->chaveamento_gerado === true;
    }

    /**
     * Retorna as posições por fase
     */
    public function posicoesPorFase(string $fase)
    {
        return $this->posicoes()->where('fase', $fase)->orderBy('posicao')->get();
    }

    /**
     * Retorna todas as fases do chaveamento
     */
    public function fases(): array
    {
        $fases = [];

        switch ($this->tipo_chaveamento) {
            case 'eliminacao_simples':
                $participantes = $this->total_participantes;

                if ($participantes >= 16) {
                    $fases[] = 'oitavas';
                }
                if ($participantes >= 8) {
                    $fases[] = 'quartas';
                }
                if ($participantes >= 4) {
                    $fases[] = 'semi';
                }
                $fases[] = 'final';

                if ($this->disputa_terceiro_lugar) {
                    $fases[] = 'disputa_terceiro';
                }
                break;

            case 'grupos_eliminatorias':
                $fases[] = 'fase_grupos';
                $fases[] = 'oitavas';
                $fases[] = 'quartas';
                $fases[] = 'semi';
                $fases[] = 'final';

                if ($this->disputa_terceiro_lugar) {
                    $fases[] = 'disputa_terceiro';
                }
                break;
        }

        return $fases;
    }
}
