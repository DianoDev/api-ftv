<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Databases\Contracts\SolicitacoesRachaContract;
use App\Http\Requests\SolicitacoesRachaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitacaoRachaController extends Controller
{
    public function __construct(private readonly SolicitacoesRachaContract $solicitacoesRachaRepository)
    {
    }

    /**
     * Listar todas as solicitações de racha (com filtros)
     */
    public function list(Request $request): JsonResponse
    {
        $filters = $request->all();

        // Por padrão, mostrar apenas solicitações abertas e futuras
        if (!isset($filters['status']) && !isset($filters['criador_id'])) {
            $filters['abertas'] = true;
        }

        $dados = $this->solicitacoesRachaRepository->paginate($filters)->toArray();

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    /**
     * Listar solicitações criadas pelo usuário autenticado
     */
    public function minhasSolicitacoes(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $filters = $request->all();
        $filters['criador_id'] = $user->id;

        $dados = $this->solicitacoesRachaRepository->paginate($filters)->toArray();

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    /**
     * Visualizar detalhes de uma solicitação
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Buscar com relacionamentos
            $solicitacao = \App\Databases\Models\SolicitacoesRacha::with([
                'arena:id,nome,cidade,estado,endereco,telefone',
                'criador:id,name,email',
                'participantes' => function ($query) {
                    $query->with('usuario:id,name,email');
                }
            ])->findOrFail($id);

            // Adicionar informação se o usuário atual está participando
            $user = Auth::user();
            $isParticipating = false;
            $isCriador = false;

            if ($user) {
                $isCriador = $solicitacao->criador_id === $user->id;
                $isParticipating = DB::table('participantes_solicitacao')
                    ->where('solicitacao_id', $id)
                    ->where('usuario_id', $user->id)
                    ->exists();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'solicitacao' => $solicitacao,
                    'is_participating' => $isParticipating,
                    'is_criador' => $isCriador,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitação não encontrada'
            ], 404);
        }
    }

    /**
     * Criar nova solicitação de racha
     */
    public function create(SolicitacoesRachaRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $params = $request->validated();
        $params['criador_id'] = $user->id;

        try {
            $this->solicitacoesRachaRepository->create($params);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de racha criada com sucesso!'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar solicitação de racha
     */
    public function update(SolicitacoesRachaRequest $request, int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Verificar se o usuário é o criador da solicitação
            if ($solicitacao->criador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para atualizar esta solicitação'
                ], 403);
            }

            $params = $request->validated();

            // Não permitir alterar o criador
            unset($params['criador_id']);

            $this->solicitacoesRachaRepository->update($id, $params);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação atualizada com sucesso!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitação não encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar/excluir solicitação de racha
     */
    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Verificar se o usuário é o criador da solicitação
            if ($solicitacao->criador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para excluir esta solicitação'
                ], 403);
            }

            $this->solicitacoesRachaRepository->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação excluída com sucesso!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitação não encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar status da solicitação
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $request->validate([
            'status' => 'required|string|in:aberta,confirmada,cancelada,concluida'
        ]);

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Verificar se o usuário é o criador
            if ($solicitacao->criador_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para atualizar o status desta solicitação'
                ], 403);
            }

            $this->solicitacoesRachaRepository->update($id, [
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Participar de uma solicitação de racha
     */
    public function participar(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Verificar se a solicitação está aberta
            if ($solicitacao->status !== 'aberta') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta solicitação não está mais aberta'
                ], 400);
            }

            // Verificar se usuário é o criador
            if ($solicitacao->criador_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você é o criador desta solicitação'
                ], 400);
            }

            // Verificar se já está participando
            $jaParticipa = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->where('usuario_id', $user->id)
                ->exists();

            if ($jaParticipa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já está participando desta solicitação'
                ], 400);
            }

            // Verificar limite de participantes
            $participantesAtuais = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->count();

            // +1 para contar o criador
            if (($participantesAtuais + 1) >= $solicitacao->limite_participantes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta solicitação já atingiu o limite de participantes'
                ], 400);
            }

            // Adicionar participante
            DB::table('participantes_solicitacao')->insert([
                'solicitacao_id' => $id,
                'usuario_id' => $user->id,
                'status' => 'interessado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Você entrou no racha com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao participar da solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sair de uma solicitação de racha
     */
    public function sair(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Verificar se usuário é o criador
            if ($solicitacao->criador_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você é o criador desta solicitação, não pode sair. Cancele a solicitação se desejar.'
                ], 400);
            }

            // Verificar se está participando
            $participacao = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->where('usuario_id', $user->id)
                ->first();

            if (!$participacao) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não está participando desta solicitação'
                ], 400);
            }

            // Remover participante
            DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->where('usuario_id', $user->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Você saiu do racha com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao sair da solicitação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar jogadores disponíveis para convidar
     */
    public function jogadoresDisponiveis(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Buscar IDs de quem já está participando
            $participantesIds = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->pluck('usuario_id')
                ->toArray();

            // Adicionar o criador à lista de participantes
            $participantesIds[] = $solicitacao->criador_id;

            // Buscar jogadores que não estão participando
            $query = DB::table('users')
                ->join('jogadores', 'users.id', '=', 'jogadores.user_id')
                ->whereNotIn('users.id', $participantesIds)
                ->select('users.id', 'users.name', 'users.email', 'jogadores.nivel_habilidade', 'jogadores.cidade', 'jogadores.estado');

            // Filtro por nome/email
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('users.name', 'ilike', "%{$search}%")
                      ->orWhere('users.email', 'ilike', "%{$search}%");
                });
            }

            $jogadores = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $jogadores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar jogadores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convidar um jogador para a solicitação
     */
    public function convidar(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        $request->validate([
            'usuario_id' => 'required|integer|exists:users,id'
        ]);

        try {
            $solicitacao = $this->solicitacoesRachaRepository->getById($id);

            // Verificar se o usuário é o criador ou está participando
            $isCriador = $solicitacao->criador_id === $user->id;
            $isParticipante = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->where('usuario_id', $user->id)
                ->exists();

            if (!$isCriador && !$isParticipante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para convidar jogadores'
                ], 403);
            }

            // Verificar se a solicitação está aberta
            if ($solicitacao->status !== 'aberta') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta solicitação não está mais aberta'
                ], 400);
            }

            $usuarioConvidadoId = $request->usuario_id;

            // Verificar se já está participando
            $jaParticipa = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->where('usuario_id', $usuarioConvidadoId)
                ->exists();

            if ($jaParticipa || $usuarioConvidadoId === $solicitacao->criador_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este jogador já está participando'
                ], 400);
            }

            // Verificar limite de participantes
            $participantesAtuais = DB::table('participantes_solicitacao')
                ->where('solicitacao_id', $id)
                ->count();

            // +1 para contar o criador
            if (($participantesAtuais + 1) >= $solicitacao->limite_participantes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta solicitação já atingiu o limite de participantes'
                ], 400);
            }

            // Adicionar participante como "convidado"
            DB::table('participantes_solicitacao')->insert([
                'solicitacao_id' => $id,
                'usuario_id' => $usuarioConvidadoId,
                'status' => 'convidado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jogador convidado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao convidar jogador: ' . $e->getMessage()
            ], 500);
        }
    }
}
