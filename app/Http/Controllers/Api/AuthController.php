<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterJogadorRequest;
use App\Http\Requests\RegisterProfessorRequest;
use App\Http\Requests\RegisterArenaRequest;
use App\Models\User;
use App\Models\Jogador;
use App\Models\Professor;
use App\Models\Arena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


    public function teste()
    {
      return response()->json(['oi']);
    }

    /**
     * Registro de Jogador
     */
    public function registerJogador(RegisterJogadorRequest $request)
    {
        try {
            DB::beginTransaction();

            // Criar usuário
            $user = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'jogador',
                'phone' => $request->phone,
            ]);

            // Criar perfil de jogador
            $jogador = Jogador::create([
                'user_id' => $user->id,
                'data_nascimento' => $request->data_nascimento,
                'genero' => $request->genero,
                'cpf' => $request->cpf,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'nivel_habilidade' => $request->nivel_habilidade ?? 'iniciante',
                'posicao_preferida' => $request->posicao_preferida,
                'bio' => $request->bio,
            ]);

            // Criar token de acesso
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jogador registrado com sucesso!',
                'data' => [
                    'user' => $user,
                    'jogador' => $jogador,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar jogador.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registro de Professor
     */
    public function registerProfessor(RegisterProfessorRequest $request)
    {
        try {
            DB::beginTransaction();

            // Criar usuário
            $user = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'professor',
                'phone' => $request->phone,
            ]);

            // Criar perfil de professor
            $professor = Professor::create([
                'user_id' => $user->id,
                'cpf' => $request->cpf,
                'registro_profissional' => $request->registro_profissional,
                'data_nascimento' => $request->data_nascimento,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'especialidades' => $request->especialidades,
                'anos_experiencia' => $request->anos_experiencia ?? 0,
                'preco_hora_aula' => $request->preco_hora_aula,
                'bio' => $request->bio,
                'certificacoes' => $request->certificacoes,
                'disponivel' => true,
            ]);

            // Criar token de acesso
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Professor registrado com sucesso!',
                'data' => [
                    'user' => $user,
                    'professor' => $professor,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar professor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registro de Arena
     */
    public function registerArena(RegisterArenaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Criar usuário
            $user = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'arena',
                'phone' => $request->phone,
            ]);

            // Criar perfil de arena
            $arena = Arena::create([
                'user_id' => $user->id,
                'nome_estabelecimento' => $request->nome_estabelecimento,
                'cnpj' => $request->cnpj,
                'endereco' => $request->endereco,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'cep' => $request->cep,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'numero_quadras' => $request->numero_quadras,
                'comodidades' => $request->comodidades,
                'horario_abertura' => $request->horario_abertura,
                'horario_fechamento' => $request->horario_fechamento,
                'dias_funcionomento' => $request->dias_funcionomento,
                'preco_hora_quadra' => $request->preco_hora_quadra,
                'descricao' => $request->descricao,
                'ativa' => true,
            ]);

            // Criar token de acesso
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Arena registrada com sucesso!',
                'data' => [
                    'user' => $user,
                    'arena' => $arena,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar arena.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Deletar tokens antigos
        $user->tokens()->delete();

        // Criar novo token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Carregar dados do perfil baseado no tipo de usuário
        $profileData = null;
        if ($user->isJogador()) {
            $profileData = $user->jogador;
        } elseif ($user->isProfessor()) {
            $profileData = $user->professor;
        } elseif ($user->isArena()) {
            $profileData = $user->arena;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'data' => [
                'user' => $user,
                'profile' => $profileData,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!',
        ]);
    }

    /**
     * Obter usuário autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Carregar dados do perfil baseado no tipo de usuário
        $profileData = null;
        if ($user->isJogador()) {
            $profileData = $user->jogador;
        } elseif ($user->isProfessor()) {
            $profileData = $user->professor;
        } elseif ($user->isArena()) {
            $profileData = $user->arena;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'profile' => $profileData,
            ],
        ]);
    }
}
