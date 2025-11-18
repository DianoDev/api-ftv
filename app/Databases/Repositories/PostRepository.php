<?php

namespace App\Databases\Repositories;

use App\Databases\Contracts\PostContract;
use App\Databases\Models\Post;
use App\Databases\Models\Amizades;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostRepository implements PostContract
{
    /**
     * @var Post
     */
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Criar um novo post
     */
    public function criar(array $data): Post
    {
        DB::beginTransaction();

        try {
            $post = $this->model->create($data);

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Buscar post por ID
     */
    public function buscarPorId(int $id): ?Post
    {
        return $this->model
            ->with('usuario')
            ->find($id);
    }

    /**
     * Listar posts válidos (ativos e não expirados)
     */
    public function listarValidos(int $perPage = 10)
    {
        return $this->model
            ->with('usuario')
            ->validos()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Listar posts de um usuário específico
     */
    public function listarPorUsuario(int $usuarioId, bool $apenasValidos = true): Collection
    {
        $query = $this->model
            ->with('usuario')
            ->where('usuario_id', $usuarioId);

        if ($apenasValidos) {
            $query->validos();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Listar posts de amigos de um usuário
     */
    public function listarPostsAmigos(int $usuarioId, int $perPage = 10)
    {
        // Buscar IDs dos amigos
        $amigosIds = Amizades::where(function ($query) use ($usuarioId) {
            $query->where('usuario_id', $usuarioId)
                ->orWhere('amigo_id', $usuarioId);
        })
            ->where('status', Amizades::STATUS_ACEITO)
            ->get()
            ->map(function ($amizade) use ($usuarioId) {
                return $amizade->usuario_id === $usuarioId
                    ? $amizade->amigo_id
                    : $amizade->usuario_id;
            })
            ->toArray();

        // Incluir posts do próprio usuário
        $amigosIds[] = $usuarioId;

        return $this->model
            ->with('usuario')
            ->whereIn('usuario_id', $amigosIds)
            ->validos()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Deletar um post
     */
    public function deletar(int $id): bool
    {
        DB::beginTransaction();

        try {
            $post = $this->model->find($id);

            if (!$post) {
                throw new \Exception('Post não encontrado');
            }

            // Deletar imagem se existir
            if ($post->imagem && file_exists(storage_path('app/public/' . $post->imagem))) {
                unlink(storage_path('app/public/' . $post->imagem));
            }

            $deleted = $post->delete();

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Desativar posts expirados
     */
    public function desativarExpirados(): int
    {
        return $this->model
            ->where('ativo', true)
            ->where('expira_em', '<=', Carbon::now())
            ->update(['ativo' => false]);
    }

    /**
     * Verificar se usuário é dono do post
     */
    public function usuarioDono(int $postId, int $usuarioId): bool
    {
        return $this->model
            ->where('id', $postId)
            ->where('usuario_id', $usuarioId)
            ->exists();
    }
}
