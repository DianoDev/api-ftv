# Guia de Teste das APIs

## Pré-requisitos

1. Certifique-se de que o Docker está rodando:
```bash
docker-compose up -d
```

2. Execute as migrations:
```bash
docker-compose exec app php artisan migrate
```

## Fluxo de Teste Completo

### 1. Registrar um Usuário Jogador

```bash
curl -X POST http://localhost:8000/api/auth/register/jogador \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@example.com",
    "password": "senha12345",
    "password_confirmation": "senha12345",
    "phone": "11999999999",
    "data_nascimento": "1990-01-01",
    "genero": "masculino",
    "cpf": "12345678901",
    "cidade": "São Paulo",
    "estado": "SP",
    "nivel_habilidade": "intermediario",
    "posicao_preferida": "atacante",
    "bio": "Jogador amador de beach tennis"
  }'
```

**Salve o token retornado para os próximos passos!**

### 2. Fazer Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "joao@example.com",
    "password": "senha12345"
  }'
```

### 3. Criar Perfil de Jogador

```bash
curl -X POST http://localhost:8000/api/jogador \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "nivel": "intermediario",
    "lado_preferido": "direita",
    "posicao_preferida": "atacante",
    "nivel_jogo": "intermediario"
  }'
```

### 4. Obter Perfil do Jogador

```bash
curl -X GET http://localhost:8000/api/jogador/me \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 5. Atualizar Perfil do Jogador

```bash
curl -X PUT http://localhost:8000/api/jogador \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "nivel": "avancado",
    "ranking": 1200
  }'
```

### 6. Listar Arenas Disponíveis

```bash
curl -X GET "http://localhost:8000/api/arenas?per_page=10&page=1"
```

### 7. Buscar Arenas por Nome

```bash
curl -X GET "http://localhost:8000/api/arenas/buscar?q=praia"
```

### 8. Criar uma Arena (como proprietário)

Primeiro, registre-se como arena:

```bash
curl -X POST http://localhost:8000/api/auth/register/arena \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Arena Praia do Sol",
    "email": "arena@example.com",
    "password": "senha12345",
    "password_confirmation": "senha12345",
    "phone": "11888888888",
    "cnpj": "12345678000199",
    "endereco": "Rua da Praia, 123",
    "cidade": "Rio de Janeiro",
    "estado": "RJ",
    "cep": "20000-000"
  }'
```

Depois crie a arena usando o token de proprietário:

```bash
curl -X POST http://localhost:8000/api/Arenas \
  -H "Authorization: Bearer TOKEN_DO_PROPRIETARIO" \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Arena Praia do Sol",
    "descricao": "Arena de beach tennis à beira mar",
    "cnpj": "12345678000199",
    "endereco": "Rua da Praia, 123",
    "cidade": "Rio de Janeiro",
    "estado": "RJ",
    "cep": "20000-000",
    "telefone": "11888888888",
    "whatsapp": "11888888888",
    "latitude": -22.906847,
    "longitude": -43.172897,
    "horario_funcionamento": {
      "segunda": "08:00-22:00",
      "terca": "08:00-22:00",
      "quarta": "08:00-22:00",
      "quinta": "08:00-22:00",
      "sexta": "08:00-23:00",
      "sabado": "07:00-23:00",
      "domingo": "07:00-20:00"
    },
    "comodidades": ["estacionamento", "vestiário", "bar", "loja"]
  }'
```

### 9. Criar uma Solicitação de Racha

```bash
curl -X POST http://localhost:8000/api/minhas-solicitacoes-racha \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "arena_id": 1,
    "data_jogo": "2024-12-15",
    "hora_inicio": "18:00",
    "hora_fim": "20:00",
    "limite_participantes": 8,
    "valor_estimado": 200.00,
    "valor_por_pessoa": 25.00,
    "nivel_sugerido": "intermediario",
    "descricao": "Racha de sexta-feira após o trabalho",
    "observacoes": "Trazer bola amarela. Organização de duplas no local."
  }'
```

### 10. Listar Minhas Solicitações

```bash
curl -X GET http://localhost:8000/api/minhas-solicitacoes-racha \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 11. Listar Todas as Solicitações Abertas

```bash
curl -X GET "http://localhost:8000/api/solicitacoes-racha-public?per_page=10"
```

### 12. Visualizar uma Solicitação Específica

```bash
curl -X GET http://localhost:8000/api/solicitacoes-racha-public/1
```

### 13. Atualizar uma Solicitação

```bash
curl -X PUT http://localhost:8000/api/minhas-solicitacoes-racha/1 \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "limite_participantes": 10,
    "descricao": "Racha atualizado - mais vagas disponíveis!"
  }'
```

### 14. Atualizar Status da Solicitação

```bash
curl -X PATCH http://localhost:8000/api/minhas-solicitacoes-racha/1/status \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "confirmada"
  }'
```

### 15. Buscar Arenas Próximas

```bash
curl -X GET "http://localhost:8000/api/arenas/proximas?latitude=-23.550520&longitude=-46.633308&raio_km=10"
```

### 16. Ver Ranking de Jogadores

```bash
curl -X GET "http://localhost:8000/api/jogador/ranking?per_page=20" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 17. Cancelar uma Solicitação

```bash
curl -X DELETE http://localhost:8000/api/minhas-solicitacoes-racha/1 \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 18. Logout

```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

---

## Testando com Postman/Insomnia

1. Importe a collection criando requests com os endpoints acima
2. Configure uma variável de ambiente para o `token`
3. Use `{{token}}` nos headers Authorization

---

## Verificando Erros

Se algo não funcionar, verifique:

1. **Banco de dados**: Certifique-se de que as migrations foram executadas
2. **Token**: Verifique se o token está sendo enviado corretamente
3. **Validação**: Leia as mensagens de erro de validação
4. **Logs**: Verifique os logs do Laravel em `storage/logs/laravel.log`

---

## Exemplos de Resposta

### Sucesso ao criar solicitação:
```json
{
  "success": true,
  "message": "Solicitação de racha criada com sucesso!"
}
```

### Erro de validação:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "data_jogo": [
      "A data do jogo deve ser hoje ou no futuro."
    ],
    "limite_participantes": [
      "Deve haver no mínimo 2 participantes."
    ]
  }
}
```

### Erro de autenticação:
```json
{
  "success": false,
  "message": "Usuário não autenticado"
}
```
