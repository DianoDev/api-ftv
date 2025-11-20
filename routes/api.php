<?php

use App\Http\Controllers\Admin\ArenasController;
use App\Http\Controllers\Admin\AulasController;
use App\Http\Controllers\Admin\CampeonatosController;
use App\Http\Controllers\Admin\CategoriaCampeonatoController;
use App\Http\Controllers\Admin\ChaveamentoController;
use App\Http\Controllers\Admin\PartidaController;
use App\Http\Controllers\Admin\JogadoresController;
use App\Http\Controllers\Admin\ProfessoresController;
use App\Http\Controllers\Admin\QuadrasController;
use App\Http\Controllers\Admin\RachasController;
use App\Http\Controllers\Admin\SolicitacoesRachaController;
use App\Http\Controllers\Users\RegisterController;
use App\Http\Controllers\Users\JogadorController;
use App\Http\Controllers\Users\SolicitacaoRachaController;
use App\Http\Controllers\Users\ArenaController;
use App\Http\Controllers\Users\AmizadeController;
use App\Http\Controllers\Users\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/teste', [AuthController::class, 'teste']);
// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::get('/teste', [AuthController::class, 'teste']);
    Route::post('/register/jogador', [AuthController::class, 'registerJogador']);
    Route::post('/register/professor', [AuthController::class, 'registerProfessor']);
    Route::post('/register/arena', [AuthController::class, 'registerArena']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas públicas de arenas
Route::prefix('arenas')->group(function () {
    Route::get('/', [ArenaController::class, 'list'])->name('arenas.public.list');
    Route::get('/buscar', [ArenaController::class, 'buscar'])->name('arenas.public.buscar');
    Route::get('/buscar-por-localizacao', [ArenaController::class, 'buscarPorLocalizacao'])->name('arenas.public.localizacao');
    Route::get('/proximas', [ArenaController::class, 'buscarProximas'])->name('arenas.public.proximas');
    Route::get('/{id}', [ArenaController::class, 'show'])->name('arenas.public.show');
});

// Rotas públicas de solicitações de racha (listagem)
Route::prefix('solicitacoes-racha-public')->group(function () {
    Route::get('/', [SolicitacaoRachaController::class, 'list'])->name('solicitacoes_racha.public.list');
    Route::get('/{id}', [SolicitacaoRachaController::class, 'show'])->name('solicitacoes_racha.public.show');
});

// Rotas protegidas (requer autenticação)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Rotas do perfil de jogador
    Route::prefix('jogador')->group(function () {
        Route::get('/me', [JogadorController::class, 'me'])->name('jogador.me');
        Route::post('/', [JogadorController::class, 'create'])->name('jogador.create');
        Route::put('/', [JogadorController::class, 'update'])->name('jogador.update');
        Route::get('/ranking', [JogadorController::class, 'ranking'])->name('jogador.ranking');
        Route::get('/{id}', [JogadorController::class, 'show'])->name('jogador.show');
    });

    // Rotas de solicitações de racha para usuários
    Route::prefix('minhas-solicitacoes-racha')->group(function () {
        Route::get('/', [SolicitacaoRachaController::class, 'minhasSolicitacoes'])->name('solicitacoes_racha.user.minhas');
        Route::post('/', [SolicitacaoRachaController::class, 'create'])->name('solicitacoes_racha.user.create');
        Route::put('/{id}', [SolicitacaoRachaController::class, 'update'])->name('solicitacoes_racha.user.update');
        Route::delete('/{id}', [SolicitacaoRachaController::class, 'delete'])->name('solicitacoes_racha.user.delete');
        Route::patch('/{id}/status', [SolicitacaoRachaController::class, 'updateStatus'])->name('solicitacoes_racha.user.status');
    });

    // Rotas de participação em rachas
    Route::prefix('solicitacoes-racha')->group(function () {
        Route::get('/{id}', [SolicitacaoRachaController::class, 'show'])->name('solicitacoes_racha.show');
        Route::post('/{id}/participar', [SolicitacaoRachaController::class, 'participar'])->name('solicitacoes_racha.participar');
        Route::delete('/{id}/sair', [SolicitacaoRachaController::class, 'sair'])->name('solicitacoes_racha.sair');
        Route::get('/{id}/jogadores-disponiveis', [SolicitacaoRachaController::class, 'jogadoresDisponiveis'])->name('solicitacoes_racha.jogadores_disponiveis');
        Route::post('/{id}/convidar', [SolicitacaoRachaController::class, 'convidar'])->name('solicitacoes_racha.convidar');
    });

    // Rotas de usuários para amizade
    Route::prefix('usuarios')->group(function () {
        Route::get('/buscar', [AmizadeController::class, 'buscarUsuarios'])->name('usuarios.buscar'); // Buscar usuários
    });

    // Rotas de amizade
    Route::prefix('amizades')->group(function () {
        Route::get('/', [AmizadeController::class, 'index'])->name('amizades.index'); // Listar amigos
        Route::get('/meus-amigos', [AmizadeController::class, 'index'])->name('amizades.meus-amigos'); // Listar amigos (alias)
        Route::get('/solicitacoes-pendentes', [AmizadeController::class, 'solicitacoesPendentes'])->name('amizades.solicitacoes-pendentes'); // Solicitações recebidas
        Route::post('/enviar-solicitacao', [AmizadeController::class, 'enviarSolicitacao'])->name('amizades.enviar-solicitacao'); // Enviar solicitação
        Route::post('/enviar', [AmizadeController::class, 'enviarSolicitacao'])->name('amizades.enviar'); // Enviar solicitação (alias)
        Route::post('/{id}/aceitar', [AmizadeController::class, 'aceitarSolicitacao'])->name('amizades.aceitar'); // Aceitar solicitação
        Route::post('/{id}/recusar', [AmizadeController::class, 'recusarSolicitacao'])->name('amizades.recusar'); // Recusar solicitação
        Route::get('/pendentes', [AmizadeController::class, 'solicitacoesPendentes'])->name('amizades.pendentes'); // Solicitações recebidas
        Route::get('/enviadas', [AmizadeController::class, 'solicitacoesEnviadas'])->name('amizades.enviadas'); // Solicitações enviadas
        Route::delete('/{amigoId}', [AmizadeController::class, 'removerAmizade'])->name('amizades.remover'); // Remover amigo
        Route::post('/bloquear', [AmizadeController::class, 'bloquearUsuario'])->name('amizades.bloquear'); // Bloquear usuário
        Route::delete('/bloquear/{bloqueadoId}', [AmizadeController::class, 'desbloquearUsuario'])->name('amizades.desbloquear'); // Desbloquear usuário
        Route::get('/bloqueados', [AmizadeController::class, 'usuariosBloqueados'])->name('amizades.bloqueados'); // Listar bloqueados
        Route::get('/verificar/{amigoId}', [AmizadeController::class, 'verificarAmizade'])->name('amizades.verificar'); // Verificar amizade
    });

    // Rotas de posts
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.index'); // Listar todos posts válidos
        Route::get('/amigos', [PostController::class, 'postsAmigos'])->name('posts.amigos'); // Posts de amigos
        Route::get('/meus', [PostController::class, 'meusPosts'])->name('posts.meus'); // Meus posts
        Route::get('/{id}', [PostController::class, 'show'])->name('posts.show'); // Ver post específico
        Route::post('/', [PostController::class, 'store'])->name('posts.store'); // Criar post
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy'); // Deletar post
        Route::post('/limpar-expirados', [PostController::class, 'limparExpirados'])->name('posts.limpar'); // Limpar expirados
    });
});
Route::group(['prefix' => 'register'], function () {
    Route::get('/', [RegisterController::class, 'index'])->name('users.index');
    Route::get('/list', [RegisterController::class, 'list'])->name('users.list');
    Route::get('/{id}', [RegisterController::class, 'edit'])->name('users.edit');
    Route::post('/', [RegisterController::class, 'create'])->name('users.create');
    Route::post('/{id}', [RegisterController::class, 'update'])->name('users.update');
    Route::delete('/{id}', [RegisterController::class, 'delete'])->name('users.delete');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'Aulas'], function () {
        Route::get('/', [AulasController::class, 'index'])->name('aulas.index');
        Route::get('/list', [AulasController::class, 'list'])->name('aulas.list');
        Route::get('/{id}', [AulasController::class, 'edit'])->name('aulas.edit');
        Route::post('/', [AulasController::class, 'create'])->name('aulas.create');
        Route::post('/{id}', [AulasController::class, 'update'])->name('aulas.update');
        Route::delete('/{id}', [AulasController::class, 'delete'])->name('aulas.delete');
    });
    Route::group(['prefix' => 'Arenas'], function () {
        Route::get('/', [ArenasController::class, 'index'])->name('Arenas.index');
        Route::get('/list', [ArenasController::class, 'list'])->name('Arenas.list');
        Route::get('/{id}', [ArenasController::class, 'edit'])->name('Arenas.edit');
        Route::post('/', [ArenasController::class, 'create'])->name('Arenas.create');
        Route::post('/{id}', [ArenasController::class, 'update'])->name('Arenas.update');
        Route::delete('/{id}', [ArenasController::class, 'delete'])->name('Arenas.delete');
    });

    Route::group(['prefix' => 'campeonatos'], function () {
        Route::get('/list', [CampeonatosController::class, 'list'])->name('campeonatos.list');
        Route::get('/{id}', [CampeonatosController::class, 'edit'])->name('campeonatos.edit');
        Route::post('/', [CampeonatosController::class, 'create'])->name('campeonatos.create');
        Route::post('/{id}', [CampeonatosController::class, 'update'])->name('campeonatos.update');
        Route::delete('/{id}', [CampeonatosController::class, 'delete'])->name('campeonatos.delete');
    });
    Route::group(['prefix' => 'jogadores'], function () {
        Route::get('/', [JogadoresController::class, 'index'])->name('jogadores.index');
        Route::get('/list', [JogadoresController::class, 'list'])->name('jogadores.list');
        Route::get('/{id}', [JogadoresController::class, 'edit'])->name('jogadores.edit');
        Route::post('/', [JogadoresController::class, 'create'])->name('jogadores.create');
        Route::post('/{id}', [JogadoresController::class, 'update'])->name('jogadores.update');
        Route::delete('/{id}', [JogadoresController::class, 'delete'])->name('jogadores.delete');
    });
    Route::group(['prefix' => 'professores'], function () {
        Route::get('/', [ProfessoresController::class, 'index'])->name('professores.index');
        Route::get('/list', [ProfessoresController::class, 'list'])->name('professores.list');
        Route::get('/{id}', [ProfessoresController::class, 'edit'])->name('professores.edit');
        Route::post('/', [ProfessoresController::class, 'create'])->name('professores.create');
        Route::post('/{id}', [ProfessoresController::class, 'update'])->name('professores.update');
        Route::delete('/{id}', [ProfessoresController::class, 'delete'])->name('professores.delete');
    });
    Route::group(['prefix' => 'quadras'], function () {
        Route::get('/', [QuadrasController::class, 'index'])->name('quadras.index');
        Route::get('/list', [QuadrasController::class, 'list'])->name('quadras.list');
        Route::get('/{id}', [QuadrasController::class, 'edit'])->name('quadras.edit');
        Route::post('/', [QuadrasController::class, 'create'])->name('quadras.create');
        Route::post('/{id}', [QuadrasController::class, 'update'])->name('quadras.update');
        Route::delete('/{id}', [QuadrasController::class, 'delete'])->name('quadras.delete');
    });
    // Rotas de Categorias de Campeonato
    Route::group(['prefix' => 'categorias-campeonato'], function () {
        Route::get('/list', [CategoriaCampeonatoController::class, 'list'])->name('categorias_campeonato.list');
        Route::get('/{id}', [CategoriaCampeonatoController::class, 'edit'])->name('categorias_campeonato.edit');
        Route::post('/', [CategoriaCampeonatoController::class, 'create'])->name('categorias_campeonato.create');
        Route::post('/{id}', [CategoriaCampeonatoController::class, 'update'])->name('categorias_campeonato.update');
        Route::delete('/{id}', [CategoriaCampeonatoController::class, 'delete'])->name('categorias_campeonato.delete');
    });

    // Rotas de Chaveamento
    Route::group(['prefix' => 'chaveamento'], function () {
        Route::get('/categoria/{categoriaId}', [ChaveamentoController::class, 'buscarPorCategoria'])->name('chaveamento.categoria');
        Route::post('/categoria/{categoriaId}/gerar-eliminacao-simples', [ChaveamentoController::class, 'gerarEliminacaoSimples'])->name('chaveamento.gerar_eliminacao_simples');
        Route::get('/{chaveamentoId}/fases/{fase}', [ChaveamentoController::class, 'buscarPosicoesPorFase'])->name('chaveamento.posicoes_fase');
        Route::post('/posicao/{posicaoId}/resultado', [ChaveamentoController::class, 'registrarResultado'])->name('chaveamento.registrar_resultado');
        Route::get('/{chaveamentoId}/completo', [ChaveamentoController::class, 'verificarCompleto'])->name('chaveamento.verificar_completo');
        Route::get('/categoria/{categoriaId}/fases', [ChaveamentoController::class, 'listarFases'])->name('chaveamento.listar_fases');
    });

    // Rotas de Partidas
    Route::group(['prefix' => 'partidas'], function () {
        Route::get('/{id}', [PartidaController::class, 'show'])->name('partidas.show');
        Route::get('/categoria/{categoriaId}', [PartidaController::class, 'listarPorCategoria'])->name('partidas.categoria');
        Route::get('/categoria/{categoriaId}/fase/{fase}', [PartidaController::class, 'listarPorFase'])->name('partidas.fase');
        Route::get('/inscricao/{inscricaoId}', [PartidaController::class, 'listarPorInscricao'])->name('partidas.inscricao');
        Route::post('/', [PartidaController::class, 'store'])->name('partidas.create');
        Route::put('/{id}', [PartidaController::class, 'update'])->name('partidas.update');
        Route::post('/{id}/iniciar', [PartidaController::class, 'iniciar'])->name('partidas.iniciar');
        Route::post('/{id}/finalizar', [PartidaController::class, 'finalizar'])->name('partidas.finalizar');
        Route::post('/{id}/wo', [PartidaController::class, 'registrarWO'])->name('partidas.wo');
        Route::post('/{id}/placar', [PartidaController::class, 'atualizarPlacar'])->name('partidas.placar');
        Route::delete('/{id}', [PartidaController::class, 'destroy'])->name('partidas.delete');
    });
    Route::group(['prefix' => 'rachas'], function () {
        Route::get('/', [RachasController::class, 'index'])->name('rachas.index');
        Route::get('/list', [RachasController::class, 'list'])->name('rachas.list');
        Route::get('/{id}', [RachasController::class, 'edit'])->name('rachas.edit');
        Route::post('/', [RachasController::class, 'create'])->name('rachas.create');
        Route::post('/{id}', [RachasController::class, 'update'])->name('rachas.update');
        Route::delete('/{id}', [RachasController::class, 'delete'])->name('rachas.delete');
    });
    Route::group(['prefix' => 'solicitacoes-racha'], function () {
        Route::get('/', [SolicitacoesRachaController::class, 'index'])->name('solicitacoes_racha.index');
        Route::get('/list', [SolicitacoesRachaController::class, 'list'])->name('solicitacoes_racha.list');
        Route::get('/{id}', [SolicitacoesRachaController::class, 'edit'])->name('solicitacoes_racha.edit');
        Route::post('/', [SolicitacoesRachaController::class, 'create'])->name('solicitacoes_racha.create');
        Route::post('/{id}', [SolicitacoesRachaController::class, 'update'])->name('solicitacoes_racha.update');
        Route::delete('/{id}', [SolicitacoesRachaController::class, 'delete'])->name('solicitacoes_racha.delete');
    });
});
