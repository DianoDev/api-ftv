# Documentação das APIs Implementadas

## Visão Geral

Foram implementadas APIs para gerenciamento de:
1. **Perfil de Jogador** - Cadastro e gerenciamento de dados do jogador
2. **Solicitações de Racha** - Criação e gerenciamento de rachadas
3. **Arenas** - Listagem e busca de arenas

---

## 1. APIs de Jogador

### 1.1 Obter Perfil do Jogador Autenticado
```
GET /api/jogador/me
Headers: Authorization: Bearer {token}
```

**Resposta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "nivel": "intermediario",
    "lado_preferido": "ambos",
    "ranking": 1000,
    "total_rachas": 0,
    "vitorias": 0,
    "derrotas": 0,
    "posicao_preferida": "atacante",
    "nivel_jogo": "intermediario"
  }
}
```

### 1.2 Criar Perfil de Jogador
```
POST /api/jogador
Headers: Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "nivel": "intermediario",
  "lado_preferido": "ambos",
  "posicao_preferida": "atacante",
  "nivel_jogo": "intermediario"
}
```

**Campos:**
- `nivel`: iniciante, intermediario, avancado, profissional
- `lado_preferido`: esquerda, direita, ambos
- `posicao_preferida`: texto livre (ex: "atacante", "levantador")
- `nivel_jogo`: iniciante, intermediario, avancado, profissional

### 1.3 Atualizar Perfil de Jogador
```
PUT /api/jogador
Headers: Authorization: Bearer {token}
Content-Type: application/json
```

**Body:** (mesmos campos do criar, todos opcionais)

### 1.4 Visualizar Jogador por ID
```
GET /api/jogador/{id}
Headers: Authorization: Bearer {token}
```

### 1.5 Ranking de Jogadores
```
GET /api/jogador/ranking?per_page=20&page=1
Headers: Authorization: Bearer {token}
```

---

## 2. APIs de Solicitações de Racha

### 2.1 Listar Solicitações Abertas (Público)
```
GET /api/solicitacoes-racha-public?per_page=20&page=1
```

**Parâmetros opcionais:**
- `arena_id`: filtrar por arena
- `status`: aberta, confirmada, cancelada, concluida
- `data_jogo`: filtrar por data (YYYY-MM-DD)
- `abertas=true`: apenas solicitações abertas e futuras (padrão)

### 2.2 Visualizar Solicitação por ID (Público)
```
GET /api/solicitacoes-racha-public/{id}
```

### 2.3 Minhas Solicitações (Autenticado)
```
GET /api/minhas-solicitacoes-racha
Headers: Authorization: Bearer {token}
```

### 2.4 Criar Solicitação de Racha
```
POST /api/minhas-solicitacoes-racha
Headers: Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "arena_id": 1,
  "data_jogo": "2024-12-01",
  "hora_inicio": "18:00",
  "hora_fim": "20:00",
  "limite_participantes": 10,
  "valor_estimado": 100.00,
  "valor_por_pessoa": 10.00,
  "nivel_sugerido": "intermediario",
  "descricao": "Racha de final de semana",
  "observacoes": "Trazer bola"
}
```

**Campos obrigatórios:**
- `arena_id`: ID da arena (deve existir)
- `data_jogo`: data do jogo (hoje ou futuro)
- `hora_inicio`: formato HH:MM
- `hora_fim`: formato HH:MM (deve ser após hora_inicio)
- `limite_participantes`: mínimo 2, máximo 100

**Campos opcionais:**
- `valor_estimado`: valor total estimado
- `valor_por_pessoa`: valor por participante
- `nivel_sugerido`: iniciante, intermediario, avancado, profissional, misto
- `descricao`: descrição do racha (max 500 caracteres)
- `observacoes`: observações adicionais (max 1000 caracteres)
- `duracao_horas`: duração em horas (calculado automaticamente se não fornecido)

### 2.5 Atualizar Solicitação de Racha
```
PUT /api/minhas-solicitacoes-racha/{id}
Headers: Authorization: Bearer {token}
Content-Type: application/json
```

**Body:** (mesmos campos do criar, todos opcionais)

### 2.6 Cancelar/Excluir Solicitação
```
DELETE /api/minhas-solicitacoes-racha/{id}
Headers: Authorization: Bearer {token}
```

### 2.7 Atualizar Status da Solicitação
```
PATCH /api/minhas-solicitacoes-racha/{id}/status
Headers: Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "status": "confirmada"
}
```

**Status possíveis:** aberta, confirmada, cancelada, concluida

---

## 3. APIs de Arenas

### 3.1 Listar Arenas (Público)
```
GET /api/arenas?per_page=20&page=1
```

**Parâmetros opcionais:**
- `cidade`: filtrar por cidade
- `estado`: filtrar por estado (UF)
- `ativo`: true/false (padrão: true)

### 3.2 Visualizar Arena por ID (Público)
```
GET /api/arenas/{id}
```

### 3.3 Buscar Arenas por Nome/Cidade (Público)
```
GET /api/arenas/buscar?q=nome_da_arena
```

### 3.4 Buscar Arenas por Localização (Público)
```
GET /api/arenas/buscar-por-localizacao?cidade=São Paulo&estado=SP
```

### 3.5 Buscar Arenas Próximas (Público)
```
GET /api/arenas/proximas?latitude=-23.550520&longitude=-46.633308&raio_km=10
```

**Parâmetros:**
- `latitude`: latitude da localização
- `longitude`: longitude da localização
- `raio_km`: raio de busca em km (padrão: 10, max: 100)

---

## 4. Autenticação

### 4.1 Registrar Jogador
```
POST /api/auth/register/jogador
Content-Type: application/json
```

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "phone": "11999999999",
  "data_nascimento": "1990-01-01",
  "genero": "masculino",
  "cpf": "12345678901",
  "cidade": "São Paulo",
  "estado": "SP",
  "nivel_habilidade": "intermediario",
  "posicao_preferida": "atacante",
  "bio": "Jogador amador de beach tennis"
}
```

### 4.2 Login
```
POST /api/auth/login
Content-Type: application/json
```

**Body:**
```json
{
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Resposta:**
```json
{
  "success": true,
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "nome": "João Silva",
    "email": "joao@example.com",
    "tipo_usuario": "jogador"
  }
}
```

### 4.3 Logout
```
POST /api/auth/logout
Headers: Authorization: Bearer {token}
```

### 4.4 Obter Dados do Usuário
```
GET /api/auth/me
Headers: Authorization: Bearer {token}
```

---

## 5. Exemplos de Uso com cURL

### Criar Solicitação de Racha
```bash
curl -X POST http://localhost:8000/api/minhas-solicitacoes-racha \
  -H "Authorization: Bearer seu_token_aqui" \
  -H "Content-Type: application/json" \
  -d '{
    "arena_id": 1,
    "data_jogo": "2024-12-01",
    "hora_inicio": "18:00",
    "hora_fim": "20:00",
    "limite_participantes": 10,
    "nivel_sugerido": "intermediario",
    "descricao": "Racha de final de semana"
  }'
```

### Listar Arenas Próximas
```bash
curl "http://localhost:8000/api/arenas/proximas?latitude=-23.550520&longitude=-46.633308&raio_km=5"
```

### Criar Perfil de Jogador
```bash
curl -X POST http://localhost:8000/api/jogador \
  -H "Authorization: Bearer seu_token_aqui" \
  -H "Content-Type: application/json" \
  -d '{
    "nivel": "intermediario",
    "lado_preferido": "direita",
    "posicao_preferida": "atacante"
  }'
```

---

## 6. Códigos de Resposta HTTP

- `200 OK`: Requisição bem-sucedida
- `201 Created`: Recurso criado com sucesso
- `400 Bad Request`: Dados inválidos
- `401 Unauthorized`: Não autenticado
- `403 Forbidden`: Sem permissão
- `404 Not Found`: Recurso não encontrado
- `500 Internal Server Error`: Erro no servidor

---

## 7. Estrutura Padrão de Resposta

### Sucesso
```json
{
  "success": true,
  "data": {},
  "message": "Operação realizada com sucesso"
}
```

### Erro
```json
{
  "success": false,
  "message": "Descrição do erro"
}
```

### Validação
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "campo": ["Mensagem de erro"]
  }
}
```
