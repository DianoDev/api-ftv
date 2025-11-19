<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Databases\Contracts\PostContract;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $postRepository;

    public function __construct(PostContract $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Listar posts válidos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $posts = $this->postRepository->listarValidos($perPage);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar posts de amigos
     */
    public function postsAmigos(Request $request): JsonResponse
    {
        try {
            $usuarioId = $request->user()->id;
            $perPage = $request->get('per_page', 10);

            $posts = $this->postRepository->listarPostsAmigos($usuarioId, $perPage);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar posts dos amigos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar meus posts
     */
    public function meusPosts(Request $request): JsonResponse
    {
        try {
            $usuarioId = $request->user()->id;
            $posts = $this->postRepository->listarPorUsuario($usuarioId, true);

            return response()->json([
                'success' => true,
                'data' => $posts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar meus posts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar post por ID
     */
    public function show($id): JsonResponse
    {
        try {
            $post = $this->postRepository->buscarPorId($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post não encontrado',
                ], 404);
            }

            if (!$post->estaValido()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post expirado ou inativo',
                ], 410);
            }

            return response()->json([
                'success' => true,
                'data' => $post,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Criar novo post
     */
    public function store(Request $request): JsonResponse
    {
        try {

            $data = [
                'usuario_id' => $request->user()->id,
                'conteudo' => $request->conteudo,
                'expira_em' => now()->addHours(24), // Post expira em 24 horas
            ];

            $post = $this->postRepository->criar($data);

            return response()->json([
                'success' => true,
                'message' => 'Post criado com sucesso',
                'data' => $post,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deletar post
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $usuarioId = $request->user()->id;

            // Verificar se o usuário é dono do post
            if (!$this->postRepository->usuarioDono($id, $usuarioId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para deletar este post',
                ], 403);
            }

            $this->postRepository->deletar($id);

            return response()->json([
                'success' => true,
                'message' => 'Post deletado com sucesso',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Desativar posts expirados (executado via scheduler ou manualmente)
     */
    public function limparExpirados(): JsonResponse
    {
        try {
            $total = $this->postRepository->desativarExpirados();

            return response()->json([
                'success' => true,
                'message' => "Total de {$total} posts expirados desativados",
                'total' => $total,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao desativar posts expirados',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
