<?php
namespace App\Databases\Repositories;

use App\Databases\Contracts\AmizadesContract;
use App\Databases\Models\Amizades;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class AmizadesRepository implements AmizadesContract
{
    public function __construct(private Amizades $amizades)
    {
    }

    public function getById(int $id): Model
    {
        return Amizades::query()
            ->with(['usuario', 'amigo'])
            ->where('id', '=', $id)
            ->firstOrFail();
    }

    public function getAll(): Collection
    {
        return Amizades::query()->get();
    }

    public function paginate(array $pagination = [], array $columns = ['*']): LengthAwarePaginator
    {
        $query = Amizades::query()->with(['usuario', 'amigo']);

        // Filtrar por usuário
        if (isset($pagination['usuario_id'])) {
            $query->where(function ($q) use ($pagination) {
                $q->where('usuario_id', $pagination['usuario_id'])
                  ->orWhere('amigo_id', $pagination['usuario_id']);
            });
        }

        // Filtrar por status
        if (isset($pagination['status'])) {
            $query->where('status', $pagination['status']);
        }

        $query->orderBy($pagination['sort'] ?? 'created_at', $pagination['sort_direction'] ?? 'desc');
        return $query->paginate($pagination['per_page'] ?? 10, $columns, 'page', $pagination['current_page'] ?? 1);
    }

    public function create(array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $amizade = new Amizades([
                'usuario_id' => $params['usuario_id'],
                'amigo_id' => $params['amigo_id'],
                'status' => $params['status'] ?? Amizades::STATUS_PENDENTE,
            ]);
            $amizade->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw $ex;
        }
    }

    public function update(int $id, array $params, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $amizade = Amizades::findOrFail($id);
            $amizade->fill($params);

            // Se for aceitar, adicionar timestamp
            if (isset($params['status']) && $params['status'] === Amizades::STATUS_ACEITO) {
                $amizade->aceito_em = now();
            }

            $amizade->save();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw $ex;
        }
    }

    public function destroy(int $id, bool $autoCommit = true): bool
    {
        $autoCommit && DB::beginTransaction();
        try {
            $amizade = Amizades::findOrFail($id);
            $amizade->delete();

            $autoCommit && DB::commit();
            return true;
        } catch (Exception $ex) {
            $autoCommit && DB::rollBack();
            throw $ex;
        }
    }

    public function enviarSolicitacao(int $usuarioId, int $amigoId): bool
    {
        DB::beginTransaction();
        try {
            // Verificar se não são o mesmo usuário
            if ($usuarioId === $amigoId) {
                throw new Exception('Você não pode enviar solicitação de amizade para si mesmo');
            }

            // Verificar se já existe uma solicitação
            $existente = $this->verificarAmizade($usuarioId, $amigoId);
            if ($existente) {
                if ($existente->status === Amizades::STATUS_BLOQUEADO) {
                    throw new Exception('Não é possível enviar solicitação para este usuário');
                }
                if ($existente->status === Amizades::STATUS_PENDENTE) {
                    throw new Exception('Já existe uma solicitação pendente');
                }
                if ($existente->status === Amizades::STATUS_ACEITO) {
                    throw new Exception('Vocês já são amigos');
                }
            }

            // Criar solicitação
            $this->create([
                'usuario_id' => $usuarioId,
                'amigo_id' => $amigoId,
                'status' => Amizades::STATUS_PENDENTE,
            ], false);

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function aceitarSolicitacao(int $amizadeId, int $usuarioId): bool
    {
        DB::beginTransaction();
        try {
            $amizade = Amizades::findOrFail($amizadeId);

            // Verificar se o usuário é quem recebeu a solicitação
            if ($amizade->amigo_id !== $usuarioId) {
                throw new Exception('Você não pode aceitar esta solicitação');
            }

            if ($amizade->status !== Amizades::STATUS_PENDENTE) {
                throw new Exception('Esta solicitação não está mais pendente');
            }

            $this->update($amizadeId, [
                'status' => Amizades::STATUS_ACEITO,
                'aceito_em' => now(),
            ], false);

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function recusarSolicitacao(int $amizadeId, int $usuarioId): bool
    {
        DB::beginTransaction();
        try {
            $amizade = Amizades::findOrFail($amizadeId);

            // Verificar se o usuário é quem recebeu a solicitação
            if ($amizade->amigo_id !== $usuarioId) {
                throw new Exception('Você não pode recusar esta solicitação');
            }

            if ($amizade->status !== Amizades::STATUS_PENDENTE) {
                throw new Exception('Esta solicitação não está mais pendente');
            }

            // Deletar ao invés de marcar como recusado
            $this->destroy($amizadeId, false);

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function bloquearUsuario(int $usuarioId, int $bloqueadoId): bool
    {
        DB::beginTransaction();
        try {
            if ($usuarioId === $bloqueadoId) {
                throw new Exception('Você não pode bloquear a si mesmo');
            }

            // Remover amizade existente, se houver
            $amizadeExistente = $this->verificarAmizade($usuarioId, $bloqueadoId);
            if ($amizadeExistente) {
                $this->destroy($amizadeExistente->id, false);
            }

            // Criar registro de bloqueio
            $this->create([
                'usuario_id' => $usuarioId,
                'amigo_id' => $bloqueadoId,
                'status' => Amizades::STATUS_BLOQUEADO,
            ], false);

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function desbloquearUsuario(int $usuarioId, int $bloqueadoId): bool
    {
        DB::beginTransaction();
        try {
            $bloqueio = Amizades::where('usuario_id', $usuarioId)
                ->where('amigo_id', $bloqueadoId)
                ->where('status', Amizades::STATUS_BLOQUEADO)
                ->first();

            if (!$bloqueio) {
                throw new Exception('Este usuário não está bloqueado');
            }

            $this->destroy($bloqueio->id, false);

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function verificarAmizade(int $usuarioId, int $amigoId): ?Model
    {
        return Amizades::where(function ($query) use ($usuarioId, $amigoId) {
            $query->where('usuario_id', $usuarioId)->where('amigo_id', $amigoId);
        })->orWhere(function ($query) use ($usuarioId, $amigoId) {
            $query->where('usuario_id', $amigoId)->where('amigo_id', $usuarioId);
        })->first();
    }

    public function saoAmigos(int $usuarioId, int $amigoId): bool
    {
        $amizade = $this->verificarAmizade($usuarioId, $amigoId);
        return $amizade && $amizade->status === Amizades::STATUS_ACEITO;
    }

    public function getAmigos(int $usuarioId): Collection
    {
        return Amizades::where(function ($query) use ($usuarioId) {
            $query->where('usuario_id', $usuarioId)->orWhere('amigo_id', $usuarioId);
        })
            ->where('status', Amizades::STATUS_ACEITO)
            ->with(['usuario', 'amigo'])
            ->get()
            ->map(fn($amizade) => $amizade->amigo_info);
    }

    public function getSolicitacoesPendentes(int $usuarioId): Collection
    {
        return Amizades::where('amigo_id', $usuarioId)
            ->where('status', Amizades::STATUS_PENDENTE)
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($amizade) {
                return [
                    'id' => $amizade->id,
                    'usuario_id' => $amizade->usuario_id,
                    'nome' => $amizade->usuario->nome ?? 'Nome não disponível',
                    'email' => $amizade->usuario->email ?? '',
                    'created_at' => $amizade->created_at,
                ];
            });
    }

    public function getSolicitacoesEnviadas(int $usuarioId): Collection
    {
        return Amizades::where('usuario_id', $usuarioId)
            ->where('status', Amizades::STATUS_PENDENTE)
            ->with('amigo')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUsuariosBloqueados(int $usuarioId): Collection
    {
        return Amizades::where('usuario_id', $usuarioId)
            ->where('status', Amizades::STATUS_BLOQUEADO)
            ->with('amigo')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function removerAmizade(int $usuarioId, int $amigoId): bool
    {
        DB::beginTransaction();
        try {
            $amizade = Amizades::where(function ($query) use ($usuarioId, $amigoId) {
                $query->where('usuario_id', $usuarioId)->where('amigo_id', $amigoId);
            })->orWhere(function ($query) use ($usuarioId, $amigoId) {
                $query->where('usuario_id', $amigoId)->where('amigo_id', $usuarioId);
            })
            ->where('status', Amizades::STATUS_ACEITO)
            ->first();

            if (!$amizade) {
                throw new Exception('Amizade não encontrada');
            }

            $this->destroy($amizade->id, false);

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
