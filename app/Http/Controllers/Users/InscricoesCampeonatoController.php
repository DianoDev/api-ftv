<?php

namespace App\Http\Controllers\Users;

use App\Databases\Contracts\InscricoesCampeonatoContract;
use App\Databases\Models\CategoriaCampeonato;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InscricoesCampeonatoController extends Controller
{
    public function __construct(
        private readonly InscricoesCampeonatoContract $inscricoesRepository
    ) {
    }

    /**
     * Listar inscrições do usuário autenticado
     */
    public function minhasInscricoes(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = [
            'per_page' => $request->get('per_page', 10),
            'page' => $request->get('page', 1),
            'usuario_id' => $user->id,
        ];

        if ($request->has('status')) {
            $pagination['status'] = $request->get('status');
        }

        $inscricoes = $this->inscricoesRepository->getByUsuario($user->id, $pagination);

        return response()->json($inscricoes);
    }

    /**
     * Obter detalhes de uma inscrição
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $inscricao = $this->inscricoesRepository->getById($id);

        if (!$inscricao) {
            return response()->json([
                'success' => false,
                'message' => 'Inscrição não encontrada'
            ], 404);
        }

        // Verificar se o usuário tem permissão para ver esta inscrição
        $user = $request->user();
        $pertenceAoUsuario = in_array($user->id, [
            $inscricao->jogador1_id,
            $inscricao->jogador2_id,
            $inscricao->jogador3_id,
            $inscricao->jogador4_id
        ]);

        if (!$pertenceAoUsuario) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para visualizar esta inscrição'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $inscricao
        ]);
    }

    /**
     * Criar nova inscrição
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Validar dados
        $validator = Validator::make($request->all(), [
            'categoria_id' => 'required|exists:categorias_campeonato,id',
            'jogador2_id' => 'nullable|exists:users,id',
            'jogador3_id' => 'nullable|exists:users,id',
            'jogador4_id' => 'nullable|exists:users,id',
            'nome_equipe' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar categoria para verificar o tipo de inscrição
        $categoria = CategoriaCampeonato::find($request->categoria_id);
        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        // Verificar se o usuário já está inscrito nesta categoria
        $jaInscrito = $this->inscricoesRepository->usuarioJaInscrito($request->categoria_id, $user->id);
        if ($jaInscrito) {
            return response()->json([
                'success' => false,
                'message' => 'Você já possui uma inscrição nesta categoria'
            ], 400);
        }

        // Validar tipo de inscrição
        $tipoInscricao = $categoria->tipo_inscricao ?? 'dupla';
        if ($tipoInscricao === 'dupla' && !$request->jogador2_id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta categoria requer um parceiro'
            ], 400);
        }

        if ($tipoInscricao === 'solo' && $request->jogador2_id) {
            return response()->json([
                'success' => false,
                'message' => 'Esta categoria é individual e não aceita parceiros'
            ], 400);
        }

        // Verificar se o parceiro já está inscrito
        if ($request->jogador2_id) {
            $parceiroJaInscrito = $this->inscricoesRepository->usuarioJaInscrito($request->categoria_id, $request->jogador2_id);
            if ($parceiroJaInscrito) {
                return response()->json([
                    'success' => false,
                    'message' => 'O parceiro já está inscrito nesta categoria'
                ], 400);
            }
        }

        // Criar inscrição
        $data = [
            'categoria_id' => $request->categoria_id,
            'jogador1_id' => $user->id,
            'jogador2_id' => $request->jogador2_id,
            'jogador3_id' => $request->jogador3_id,
            'jogador4_id' => $request->jogador4_id,
            'nome_equipe' => $request->nome_equipe,
            'status' => 'pendente',
            'pagamento_confirmado' => false,
        ];

        try {
            $inscricao = $this->inscricoesRepository->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Inscrição realizada com sucesso!',
                'data' => $inscricao
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar inscrição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar inscrição
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $inscricao = $this->inscricoesRepository->getById($id);

        if (!$inscricao) {
            return response()->json([
                'success' => false,
                'message' => 'Inscrição não encontrada'
            ], 404);
        }

        // Verificar se o usuário é o jogador1 (principal)
        $user = $request->user();
        if ($inscricao->jogador1_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o jogador principal pode cancelar a inscrição'
            ], 403);
        }

        // Verificar se a inscrição pode ser cancelada
        if ($inscricao->status === 'cancelada') {
            return response()->json([
                'success' => false,
                'message' => 'Esta inscrição já foi cancelada'
            ], 400);
        }

        // Cancelar inscrição
        try {
            $this->inscricoesRepository->atualizarStatus($id, 'cancelada');

            return response()->json([
                'success' => true,
                'message' => 'Inscrição cancelada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar inscrição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar se o usuário está inscrito em uma categoria
     */
    public function verificarInscricao(Request $request, int $categoriaId): JsonResponse
    {
        $user = $request->user();

        $inscrito = $this->inscricoesRepository->usuarioJaInscrito($categoriaId, $user->id);

        $inscricao = null;
        if ($inscrito) {
            $inscricao = $this->inscricoesRepository->getByUsuario($user->id, [
                'categoria_id' => $categoriaId,
                'per_page' => 1
            ])->first();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'inscrito' => $inscrito,
                'inscricao' => $inscricao
            ]
        ]);
    }

    /**
     * Verificar inscrições do usuário em um campeonato
     */
    public function verificarInscricoesCampeonato(Request $request, int $campeonatoId): JsonResponse
    {
        $user = $request->user();

        // Buscar todas as categorias do campeonato
        $categorias = CategoriaCampeonato::where('campeonato_id', $campeonatoId)->get();

        $categoriasInscritas = [];

        foreach ($categorias as $categoria) {
            $inscrito = $this->inscricoesRepository->usuarioJaInscrito($categoria->id, $user->id);
            if ($inscrito) {
                $categoriasInscritas[] = $categoria->id;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'inscrito' => count($categoriasInscritas) > 0,
                'categorias' => $categoriasInscritas
            ]
        ]);
    }
}
