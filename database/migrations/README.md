# Migrations do Laravel 12 - FutApp

## 📋 Visão Geral

Este conjunto de migrations foi criado para migrar o projeto do Supabase para Laravel 12. As migrations foram organizadas de forma a respeitar as dependências entre tabelas e seguir as melhores práticas do Laravel.

## 🗂️ Estrutura de Tabelas

### Tabelas Principais

1. **users** - Tabela base de usuários com campos comuns
2. **jogadores** - Perfil específico de jogadores
3. **arenas** - Estabelecimentos/quadras esportivas
4. **quadras** - Quadras individuais de cada arena
5. **professores** - Perfil específico de professores

### Tabelas de Agendamento

6. **reservas** - Reservas de quadras
7. **aulas** - Aulas agendadas com professores

### Tabelas de Competições

8. **campeonatos** - Campeonatos organizados
9. **categorias_campeonato** - Categorias dentro dos campeonatos
10. **inscricoes_campeonato** - Inscrições nas categorias

### Tabelas Sociais

11. **posts** - Postagens dos usuários
12. **comentarios** - Comentários em posts
13. **curtidas** - Curtidas em posts

### Tabelas de Partidas (Rachas)

14. **rachas** - Partidas organizadas
15. **duplas_racha** - Duplas participantes de cada racha
16. **procura_parceiros** - Sistema para encontrar parceiros de jogo
17. **participantes_procura** - Participantes interessados em procuras
18. **solicitacoes_racha** - Vaquinhas para alugar quadras
19. **participantes_solicitacao** - Participantes de vaquinhas

### Tabelas de Sistema

20. **avaliacoes** - Sistema de avaliações (genérico)
21. **notificacoes** - Notificações dos usuários
22. **historico_ranking** - Histórico de evolução do ranking

## 🚀 Como Usar

### 1. Copiar as Migrations

Copie todos os arquivos de migration para a pasta `database/migrations/` do seu projeto Laravel.

### 2. Executar as Migrations

```bash
php artisan migrate
```

### 3. Reverter (se necessário)

```bash
php artisan migrate:rollback
```

## 📝 Alterações em Relação ao Script Original

### Mudanças Principais:

1. **Tabela Users Simplificada**: Removidos campos específicos de cada tipo de usuário
2. **Tabelas Separadas por Tipo**: Criadas tabelas `jogadores`, `arenas` e `professores` separadas
3. **Campo perfil_completo**: Adicionado para controlar se o usuário completou seu perfil
4. **Soft Deletes**: Adicionado na tabela users
5. **UUID**: Mantido uso de UUIDs como no original
6. **JSON**: Campos JSONB do PostgreSQL convertidos para JSON do Laravel

### Campos Removidos da Tabela Users:

- Campos específicos de jogador (movidos para tabela `jogadores`)
- Campos específicos de arena (mantidos na tabela `arenas`)
- Campos específicos de professor (movidos para tabela `professores`)

## 🔐 Segurança e Autenticação

As policies e triggers foram removidas conforme solicitado. Você pode implementar:

- **Policies**: Usando Laravel Policies
- **Gates**: Para autorizações específicas
- **Middleware**: Para proteção de rotas
- **Events e Listeners**: Para automações (substituindo triggers)

## 📦 Models Sugeridos

Crie os seguintes Models com seus relacionamentos:

```php
// User.php
- hasOne: Jogador, Professor
- hasMany: Arena, Post, Comentario, Curtida, Reserva, etc.

// Jogador.php
- belongsTo: User

// Arena.php
- belongsTo: User (proprietario)
- hasMany: Quadra

// Professor.php
- belongsTo: User
- hasMany: Aula

// Racha.php
- belongsTo: User (criador), Quadra
- hasMany: DuplaRacha

// etc.
```

## ⚙️ Configurações Necessárias

### 1. Instalar Pacote UUID

```bash
composer require ramsey/uuid
```

### 2. Configurar Model Base (opcional)

Crie um trait para usar UUID como primary key:

```php
// app/Traits/UsesUuid.php
trait UsesUuid
{
    protected static function bootUsesUuid()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
```

## 🎯 Próximos Passos

1. Criar os Models com relacionamentos
2. Criar Seeders para dados iniciais
3. Implementar Policies para autorização
4. Criar Events/Listeners para automações
5. Implementar validações nos Controllers/FormRequests
6. Criar API Resources para serialização de dados

## 📞 Suporte

Para dúvidas ou problemas, consulte a documentação oficial do Laravel:
- https://laravel.com/docs/12.x/migrations
- https://laravel.com/docs/12.x/eloquent-relationships

## 🔄 Fluxo de Cadastro de Usuário

1. Usuário cria conta (apenas dados básicos na tabela `users`)
2. Sistema detecta que `perfil_completo = false`
3. Redireciona para tela de completar perfil conforme `tipo_usuario`
4. Salva dados específicos na tabela correspondente:
   - Se jogador → tabela `jogadores`
   - Se arena → tabela `arenas` (já existe)
   - Se professor → tabela `professores`
5. Atualiza `perfil_completo = true` na tabela users

---

**Versão**: 1.0  
**Data**: Novembro 2024  
**Framework**: Laravel 12
