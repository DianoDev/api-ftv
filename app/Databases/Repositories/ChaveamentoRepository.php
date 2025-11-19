<?php

namespace App\Databases\Repositories;

use App\Databases\Contracts\ChaveamentoContract;
use App\Databases\Models\ChaveamentoCampeonato;
use App\Databases\Models\PosicaoChaveamento;
use App\Databases\Models\PartidaCampeonato;
use App\Databases\Models\InscricaoCampeonato;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChaveamentoRepository implements ChaveamentoContract
{
    protected $model;
    protected $posicaoModel;

    public function __construct(ChaveamentoCampeonato $model, PosicaoChaveamento $posicaoModel)
    {
        $this->model = $model;
        $this->posicaoModel = $posicaoModel;
    }

    public function buscarPorId(int $id): ?ChaveamentoCampeonato
    {
        return $this->model
            ->with(['categoria', 'posicoes.partida', 'posicoes.inscricao1.usuario', 'posicoes.inscricao2.usuario'])
            ->find($id);
    }

    public function buscarPorCategoria(int $categoriaId): ?ChaveamentoCampeonato
    {
        return $this->model
            ->with(['categoria', 'posicoes.partida', 'posicoes.inscricao1.usuario', 'posicoes.inscricao2.usuario'])
            ->where('categoria_id', $categoriaId)
            ->first();
    }

    public function criar(array $data): ChaveamentoCampeonato
    {
        DB::beginTransaction();

        try {
            $chaveamento = $this->model->create($data);

            DB::commit();

            return $chaveamento->load(['categoria', 'posicoes']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function atualizar(int $id, array $data): ChaveamentoCampeonato
    {
        DB::beginTransaction();

        try {
            $chaveamento = $this->model->findOrFail($id);
            $chaveamento->update($data);

            DB::commit();

            return $chaveamento->load(['categoria', 'posicoes']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function gerarEliminacaoSimples(int $categoriaId, array $inscricoesIds): ChaveamentoCampeonato
    {
        DB::beginTransaction();

        try {
            $totalParticipantes = count($inscricoesIds);

            // Verificar se já existe chaveamento
            $chaveamento = $this->buscarPorCategoria($categoriaId);

            if ($chaveamento && $chaveamento->foiGerado()) {
                throw new \Exception('Chaveamento já foi gerado para esta categoria');
            }

            // Criar ou atualizar chaveamento
            if (!$chaveamento) {
                $chaveamento = $this->model->create([
                    'categoria_id' => $categoriaId,
                    'tipo_chaveamento' => 'eliminacao_simples',
                    'total_participantes' => $totalParticipantes,
                    'disputa_terceiro_lugar' => true,
                    'criterio_desempate' => 'saldo_sets',
                ]);
            }

            // Calcular número de rodadas necessárias
            $proximaPotenciaDeDois = pow(2, ceil(log($totalParticipantes, 2)));
            $byes = $proximaPotenciaDeDois - $totalParticipantes;

            // Embaralhar inscrições para randomizar
            shuffle($inscricoesIds);

            // Determinar a primeira fase
            $primeiraFase = $this->determinarPrimeiraFase($proximaPotenciaDeDois);

            // Gerar posições da primeira rodada
            $posicoes = [];
            $posicao = 1;

            for ($i = 0; $i < $proximaPotenciaDeDois / 2; $i++) {
                $inscricao1Id = isset($inscricoesIds[$i * 2]) ? $inscricoesIds[$i * 2] : null;
                $inscricao2Id = isset($inscricoesIds[$i * 2 + 1]) ? $inscricoesIds[$i * 2 + 1] : null;

                $posicoes[] = $this->posicaoModel->create([
                    'chaveamento_id' => $chaveamento->id,
                    'fase' => $primeiraFase,
                    'posicao' => $posicao,
                    'ordem_exibicao' => $posicao,
                    'inscricao1_id' => $inscricao1Id,
                    'inscricao2_id' => $inscricao2Id,
                    'seed_inscricao1' => $inscricao1Id ? ($i * 2 + 1) : null,
                    'seed_inscricao2' => $inscricao2Id ? ($i * 2 + 2) : null,
                    'status' => ($inscricao1Id && $inscricao2Id) ? 'pronta' : 'aguardando',
                ]);

                $posicao++;
            }

            // Gerar fases subsequentes
            $this->gerarFasesSubsequentes($chaveamento, $primeiraFase, $posicoes);

            // Marcar chaveamento como gerado
            $chaveamento->update([
                'chaveamento_gerado' => true,
                'gerado_em' => now(),
            ]);

            DB::commit();

            return $chaveamento->load(['categoria', 'posicoes.inscricao1.usuario', 'posicoes.inscricao2.usuario']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function determinarPrimeiraFase(int $totalPosicoes): string
    {
        if ($totalPosicoes >= 16) {
            return 'oitavas';
        } elseif ($totalPosicoes >= 8) {
            return 'quartas';
        } elseif ($totalPosicoes >= 4) {
            return 'semi';
        }
        return 'final';
    }

    private function proximaFase(string $faseAtual): ?string
    {
        $fases = [
            'oitavas' => 'quartas',
            'quartas' => 'semi',
            'semi' => 'final',
            'final' => null,
        ];

        return $fases[$faseAtual] ?? null;
    }

    private function gerarFasesSubsequentes(ChaveamentoCampeonato $chaveamento, string $primeiraFase, array $posicoesAnteriores)
    {
        $faseAtual = $primeiraFase;

        while ($proximaFase = $this->proximaFase($faseAtual)) {
            $novasPosicoes = [];
            $numPosicoes = count($posicoesAnteriores) / 2;

            for ($i = 0; $i < $numPosicoes; $i++) {
                $origem1 = $posicoesAnteriores[$i * 2];
                $origem2 = $posicoesAnteriores[$i * 2 + 1];

                $novasPosicoes[] = $this->posicaoModel->create([
                    'chaveamento_id' => $chaveamento->id,
                    'fase' => $proximaFase,
                    'posicao' => $i + 1,
                    'ordem_exibicao' => $i + 1,
                    'origem_inscricao1_posicao_id' => $origem1->id,
                    'origem_inscricao2_posicao_id' => $origem2->id,
                    'status' => 'aguardando',
                ]);

                // Atualizar posições anteriores com próxima posição do vencedor
                $origem1->update(['proxima_posicao_vencedor_id' => $novasPosicoes[$i]->id]);
                $origem2->update(['proxima_posicao_vencedor_id' => $novasPosicoes[$i]->id]);
            }

            $posicoesAnteriores = $novasPosicoes;
            $faseAtual = $proximaFase;
        }

        // Criar disputa de terceiro lugar se configurado
        if ($chaveamento->disputa_terceiro_lugar && $faseAtual === 'final') {
            // Buscar as duas posições da semifinal
            $semiPosicoes = $this->posicaoModel
                ->where('chaveamento_id', $chaveamento->id)
                ->where('fase', 'semi')
                ->get();

            if ($semiPosicoes->count() === 2) {
                $disputaTerceiro = $this->posicaoModel->create([
                    'chaveamento_id' => $chaveamento->id,
                    'fase' => 'disputa_terceiro',
                    'posicao' => 1,
                    'ordem_exibicao' => 1,
                    'origem_inscricao1_posicao_id' => $semiPosicoes[0]->id,
                    'origem_inscricao2_posicao_id' => $semiPosicoes[1]->id,
                    'status' => 'aguardando',
                ]);

                // Atualizar semifinais com próxima posição do perdedor
                $semiPosicoes[0]->update(['proxima_posicao_perdedor_id' => $disputaTerceiro->id]);
                $semiPosicoes[1]->update(['proxima_posicao_perdedor_id' => $disputaTerceiro->id]);
            }
        }
    }

    public function buscarPosicoesPorFase(int $chaveamentoId, string $fase): Collection
    {
        return $this->posicaoModel
            ->with(['partida', 'inscricao1.usuario', 'inscricao2.usuario', 'inscricaoVencedora.usuario'])
            ->where('chaveamento_id', $chaveamentoId)
            ->where('fase', $fase)
            ->orderBy('posicao')
            ->get();
    }

    public function registrarResultado(int $posicaoId, int $inscricaoVencedoraId): PosicaoChaveamento
    {
        DB::beginTransaction();

        try {
            $posicao = $this->posicaoModel->findOrFail($posicaoId);

            // Validar que o vencedor é um dos competidores
            if ($posicao->inscricao1_id !== $inscricaoVencedoraId && $posicao->inscricao2_id !== $inscricaoVencedoraId) {
                throw new \Exception('Vencedor inválido para esta posição');
            }

            // Atualizar posição atual
            $posicao->update([
                'inscricao_vencedora_id' => $inscricaoVencedoraId,
                'status' => 'finalizada',
            ]);

            // Determinar perdedor
            $inscricaoPerdedoraId = $posicao->inscricao1_id === $inscricaoVencedoraId
                ? $posicao->inscricao2_id
                : $posicao->inscricao1_id;

            // Avançar vencedor para próxima fase
            if ($posicao->proxima_posicao_vencedor_id) {
                $proximaPosicao = $this->posicaoModel->find($posicao->proxima_posicao_vencedor_id);

                if ($proximaPosicao->origem_inscricao1_posicao_id === $posicao->id) {
                    $proximaPosicao->inscricao1_id = $inscricaoVencedoraId;
                } else {
                    $proximaPosicao->inscricao2_id = $inscricaoVencedoraId;
                }

                // Verificar se a próxima posição está pronta para jogar
                if ($proximaPosicao->inscricao1_id && $proximaPosicao->inscricao2_id) {
                    $proximaPosicao->status = 'pronta';
                }

                $proximaPosicao->save();
            }

            // Avançar perdedor para disputa de terceiro (se aplicável)
            if ($posicao->proxima_posicao_perdedor_id) {
                $proximaPosicaoPerdedor = $this->posicaoModel->find($posicao->proxima_posicao_perdedor_id);

                if ($proximaPosicaoPerdedor->origem_inscricao1_posicao_id === $posicao->id) {
                    $proximaPosicaoPerdedor->inscricao1_id = $inscricaoPerdedoraId;
                } else {
                    $proximaPosicaoPerdedor->inscricao2_id = $inscricaoPerdedoraId;
                }

                // Verificar se está pronta
                if ($proximaPosicaoPerdedor->inscricao1_id && $proximaPosicaoPerdedor->inscricao2_id) {
                    $proximaPosicaoPerdedor->status = 'pronta';
                }

                $proximaPosicaoPerdedor->save();
            }

            DB::commit();

            return $posicao->load(['partida', 'inscricao1.usuario', 'inscricao2.usuario', 'inscricaoVencedora.usuario']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function estaCompleto(int $chaveamentoId): bool
    {
        $chaveamento = $this->buscarPorId($chaveamentoId);

        if (!$chaveamento) {
            return false;
        }

        // Verificar se a final foi concluída
        $final = $this->posicaoModel
            ->where('chaveamento_id', $chaveamentoId)
            ->where('fase', 'final')
            ->first();

        return $final && $final->foiFinalizada();
    }

    public function buscarVencedor(int $chaveamentoId): ?int
    {
        $final = $this->posicaoModel
            ->where('chaveamento_id', $chaveamentoId)
            ->where('fase', 'final')
            ->first();

        return $final ? $final->inscricao_vencedora_id : null;
    }
}
