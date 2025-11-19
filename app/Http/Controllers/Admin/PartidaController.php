<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Databases\Contracts\PartidaContract;
use App\Databases\Contracts\ChaveamentoContract;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PartidaController extends Controller
{
    protected $partidaRepository;
    protected $chaveamentoRepository;

    public function __construct(
        PartidaContract $partidaRepository,
        ChaveamentoContract $chaveamentoRepository
    ) {
        $this->partidaRepository = $partidaRepository;
        $this->chaveamentoRepository = $chaveamentoRepository;
    }

    /**
     * Buscar partida por ID
     */
    public function show($id): JsonResponse
    {
        try {
            $partida = $this->partidaRepository->buscarPorId($id);

            if (!$partida) {
                return response()->json([
                    'success' => false,
                    'message' => 'Partida não encontrada',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $partida,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar partida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar partidas por categoria
     */
    public function listarPorCategoria($categoriaId): JsonResponse
    {
        try {
            $partidas = $this->partidaRepository->listarPorCategoria($categoriaId);

            return response()->json([
                'success' => true,
                'data' => $partidas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar partidas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar partidas por fase
     */
    public function listarPorFase($categoriaId, $fase): JsonResponse
    {
        try {
            $partidas = $this->partidaRepository->listarPorFase($categoriaId, $fase);

            return response()->json([
                'success' => true,
                'data' => $partidas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar partidas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar partidas de uma inscrição
     */
    public function listarPorInscricao($inscricaoId): JsonResponse
    {
        try {
            $partidas = $this->partidaRepository->listarPorInscricao($inscricaoId);

            return response()->json([
                'success' => true,
                'data' => $partidas,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar partidas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Criar nova partida
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'categoria_id' => 'required|integer|exists:categorias_campeonato,id',
                'inscricao1_id' => 'required|integer|exists:inscricoes_campeonato,id',
                'inscricao2_id' => 'required|integer|exists:inscricoes_campeonato,id|different:inscricao1_id',
                'fase' => 'required|string|in:oitavas,quartas,semi,final,disputa_terceiro,fase_grupos',
                'quadra_id' => 'nullable|integer|exists:quadras,id',
                'data_hora' => 'nullable|date',
                'duracao_minutos' => 'nullable|integer|min:1',
            ], [
                'categoria_id.required' => 'A categoria é obrigatória',
                'inscricao1_id.required' => 'A primeira inscrição é obrigatória',
                'inscricao2_id.required' => 'A segunda inscrição é obrigatória',
                'inscricao2_id.different' => 'As inscrições devem ser diferentes',
                'fase.required' => 'A fase é obrigatória',
                'fase.in' => 'Fase inválida',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $partida = $this->partidaRepository->criar($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Partida criada com sucesso',
                'data' => $partida,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar partida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualizar partida
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'quadra_id' => 'nullable|integer|exists:quadras,id',
                'data_hora' => 'nullable|date',
                'duracao_minutos' => 'nullable|integer|min:1',
                'arbitro_id' => 'nullable|integer|exists:users,id',
                'observacoes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $partida = $this->partidaRepository->atualizar($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Partida atualizada com sucesso',
                'data' => $partida,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar partida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Iniciar partida
     */
    public function iniciar($id): JsonResponse
    {
        try {
            $partida = $this->partidaRepository->iniciar($id);

            return response()->json([
                'success' => true,
                'message' => 'Partida iniciada com sucesso',
                'data' => $partida,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar partida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Finalizar partida
     */
    public function finalizar(Request $request, $id): JsonResponse
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

            $partida = $this->partidaRepository->finalizar($id, $request->inscricao_vencedora_id);

            // Se a partida tem posição no chaveamento, registrar resultado
            if ($partida->posicao_chaveamento_id) {
                $this->chaveamentoRepository->registrarResultado(
                    $partida->posicao_chaveamento_id,
                    $request->inscricao_vencedora_id
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Partida finalizada com sucesso',
                'data' => $partida,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar partida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar W.O.
     */
    public function registrarWO(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'inscricao_vencedora_id' => 'required|integer|exists:inscricoes_campeonato,id',
                'motivo' => 'required|string|max:500',
            ], [
                'inscricao_vencedora_id.required' => 'O vencedor é obrigatório',
                'inscricao_vencedora_id.exists' => 'Inscrição vencedora não existe',
                'motivo.required' => 'O motivo do W.O. é obrigatório',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $partida = $this->partidaRepository->registrarWO(
                $id,
                $request->inscricao_vencedora_id,
                $request->motivo
            );

            // Se a partida tem posição no chaveamento, registrar resultado
            if ($partida->posicao_chaveamento_id) {
                $this->chaveamentoRepository->registrarResultado(
                    $partida->posicao_chaveamento_id,
                    $request->inscricao_vencedora_id
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'W.O. registrado com sucesso',
                'data' => $partida,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar W.O.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualizar placar
     */
    public function atualizarPlacar(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sets_inscricao1' => 'required|integer|min:0',
                'sets_inscricao2' => 'required|integer|min:0',
            ], [
                'sets_inscricao1.required' => 'Os sets da inscrição 1 são obrigatórios',
                'sets_inscricao2.required' => 'Os sets da inscrição 2 são obrigatórios',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $partida = $this->partidaRepository->atualizarPlacar(
                $id,
                $request->sets_inscricao1,
                $request->sets_inscricao2
            );

            return response()->json([
                'success' => true,
                'message' => 'Placar atualizado com sucesso',
                'data' => $partida,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar placar',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deletar partida
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->partidaRepository->deletar($id);

            return response()->json([
                'success' => true,
                'message' => 'Partida deletada com sucesso',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar partida',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
