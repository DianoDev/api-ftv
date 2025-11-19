<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Databases\Contracts\ChaveamentoContract;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChaveamentoController extends Controller
{
    protected $chaveamentoRepository;

    public function __construct(ChaveamentoContract $chaveamentoRepository)
    {
        $this->chaveamentoRepository = $chaveamentoRepository;
    }

    /**
     * Buscar chaveamento por categoria
     */
    public function buscarPorCategoria($categoriaId): JsonResponse
    {
        try {
            $chaveamento = $this->chaveamentoRepository->buscarPorCategoria($categoriaId);

            if (!$chaveamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chaveamento não encontrado para esta categoria',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $chaveamento,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar chaveamento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gerar chaveamento de eliminação simples
     */
    public function gerarEliminacaoSimples(Request $request, $categoriaId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'inscricoes' => 'required|array|min:2',
                'inscricoes.*' => 'required|integer|exists:inscricoes_campeonato,id',
            ], [
                'inscricoes.required' => 'É necessário fornecer as inscrições',
                'inscricoes.array' => 'As inscrições devem ser um array',
                'inscricoes.min' => 'São necessárias no mínimo 2 inscrições',
                'inscricoes.*.exists' => 'Uma ou mais inscrições não existem',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $chaveamento = $this->chaveamentoRepository->gerarEliminacaoSimples(
                $categoriaId,
                $request->inscricoes
            );

            return response()->json([
                'success' => true,
                'message' => 'Chaveamento gerado com sucesso',
                'data' => $chaveamento,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar chaveamento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar posições por fase
     */
    public function buscarPosicoesPorFase($chaveamentoId, $fase): JsonResponse
    {
        try {
            $posicoes = $this->chaveamentoRepository->buscarPosicoesPorFase($chaveamentoId, $fase);

            return response()->json([
                'success' => true,
                'data' => $posicoes,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar posições',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar resultado de uma posição
     */
    public function registrarResultado(Request $request, $posicaoId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'inscricao_vencedora_id' => 'required|integer|exists:inscricoes_campeonato,id',
            ], [
                'inscricao_vencedora_id.required' => 'O vencedor é obrigatório',
                'inscricao_vencedora_id.exists' => 'Inscrição vencedora não existe',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $posicao = $this->chaveamentoRepository->registrarResultado(
                $posicaoId,
                $request->inscricao_vencedora_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Resultado registrado com sucesso',
                'data' => $posicao,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar resultado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar se chaveamento está completo
     */
    public function verificarCompleto($chaveamentoId): JsonResponse
    {
        try {
            $completo = $this->chaveamentoRepository->estaCompleto($chaveamentoId);
            $vencedor = null;

            if ($completo) {
                $vencedorId = $this->chaveamentoRepository->buscarVencedor($chaveamentoId);
                // Aqui você pode buscar os dados completos do vencedor se necessário
                $vencedor = $vencedorId;
            }

            return response()->json([
                'success' => true,
                'completo' => $completo,
                'vencedor_id' => $vencedor,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar chaveamento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar todas as fases do chaveamento
     */
    public function listarFases($categoriaId): JsonResponse
    {
        try {
            $chaveamento = $this->chaveamentoRepository->buscarPorCategoria($categoriaId);

            if (!$chaveamento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chaveamento não encontrado',
                ], 404);
            }

            $fases = $chaveamento->fases();

            $resultado = [];
            foreach ($fases as $fase) {
                $posicoes = $this->chaveamentoRepository->buscarPosicoesPorFase($chaveamento->id, $fase);
                $resultado[] = [
                    'fase' => $fase,
                    'posicoes' => $posicoes,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $resultado,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar fases',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
