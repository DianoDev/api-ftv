<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\AmizadesContract;
use App\Http\Requests\AmizadesRequest;
use Illuminate\Support\Facades\Auth;
use Exception;

class AmizadeController extends Controller
{
    public function __construct(private readonly AmizadesContract $amizadesRepository)
    {
    }

    /**
     * Listar todos os amigos do usuário autenticado
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $amigos = $this->amizadesRepository->getAmigos($user->id);

            return response()->json([
                'success' => true,
                'data' => $amigos,
                'total' => $amigos->count()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar amigos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar solicitação de amizade
     */
    public function enviarSolicitacao(AmizadesRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $amigoId = $request->input('amigo_id');

            $this->amizadesRepository->enviarSolicitacao($user->id, $amigoId);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de amizade enviada com sucesso!'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Aceitar solicitação de amizade
     */
    public function aceitarSolicitacao(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->amizadesRepository->aceitarSolicitacao($id, $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação aceita com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Recusar solicitação de amizade
     */
    public function recusarSolicitacao(int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->amizadesRepository->recusarSolicitacao($id, $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação recusada com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Listar solicitações pendentes recebidas
     */
    public function solicitacoesPendentes(): JsonResponse
    {
        try {
            $user = Auth::user();
            $solicitacoes = $this->amizadesRepository->getSolicitacoesPendentes($user->id);

            return response()->json([
                'success' => true,
                'data' => $solicitacoes,
                'total' => $solicitacoes->count()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar solicitações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar solicitações enviadas pelo usuário
     */
    public function solicitacoesEnviadas(): JsonResponse
    {
        try {
            $user = Auth::user();
            $solicitacoes = $this->amizadesRepository->getSolicitacoesEnviadas($user->id);

            return response()->json([
                'success' => true,
                'data' => $solicitacoes,
                'total' => $solicitacoes->count()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar solicitações enviadas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover amizade
     */
    public function removerAmizade(int $amigoId): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->amizadesRepository->removerAmizade($user->id, $amigoId);

            return response()->json([
                'success' => true,
                'message' => 'Amizade removida com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Bloquear usuário
     */
    public function bloquearUsuario(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $bloqueadoId = $request->input('bloqueado_id');

            $this->amizadesRepository->bloquearUsuario($user->id, $bloqueadoId);

            return response()->json([
                'success' => true,
                'message' => 'Usuário bloqueado com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Desbloquear usuário
     */
    public function desbloquearUsuario(int $bloqueadoId): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->amizadesRepository->desbloquearUsuario($user->id, $bloqueadoId);

            return response()->json([
                'success' => true,
                'message' => 'Usuário desbloqueado com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Listar usuários bloqueados
     */
    public function usuariosBloqueados(): JsonResponse
    {
        try {
            $user = Auth::user();
            $bloqueados = $this->amizadesRepository->getUsuariosBloqueados($user->id);

            return response()->json([
                'success' => true,
                'data' => $bloqueados,
                'total' => $bloqueados->count()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar usuários bloqueados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar se dois usuários são amigos
     */
    public function verificarAmizade(int $amigoId): JsonResponse
    {
        try {
            $user = Auth::user();
            $amizade = $this->amizadesRepository->verificarAmizade($user->id, $amigoId);

            if ($amizade) {
                return response()->json([
                    'success' => true,
                    'sao_amigos' => $amizade->status === 'aceito',
                    'status' => $amizade->status,
                    'data' => $amizade
                ]);
            }

            return response()->json([
                'success' => true,
                'sao_amigos' => false,
                'status' => null,
                'data' => null
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar amizade: ' . $e->getMessage()
            ], 500);
        }
    }
}
