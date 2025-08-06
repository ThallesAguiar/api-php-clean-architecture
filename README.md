# API SpinWin

Uma API RESTful seguindo os princípios da **Clean Architecture**.

## 🏗️ Arquitetura

Este projeto está organizado seguindo os princípios da **Clean Architecture** com as seguintes camadas:

- **Core**: Entidades e regras de negócio
- **Application**: Casos de uso e interfaces
- **Infrastructure**: Controllers, repositórios e adaptadores externos
- **Shared**: Utilitários e tipos comuns

## 🚀 Como Executar

> **💡 Dica:** Para um guia mais rápido, consulte [doc/quick-start.md](doc/quick-start.md)

### Opção 1: Com Docker (Recomendado)

1. **Configurar arquivo `.env`:**
   ```bash
   # Copie o arquivo de exemplo
   cp env.example .env
   ```

2. **Adicionar configurações do Docker ao `.env`:**
   ```env
   # Configurações do MySQL (para o container MySQL)
   MYSQL_ROOT_PASSWORD=root
   MYSQL_DATABASE=spinwin
   MYSQL_USER=spinwin
   MYSQL_PASSWORD=spinwin
   DB_PORT=3306

   # Configurações para o PHP (dentro do container)
   DB_HOST=mysql
   DB_USERNAME=root
   DB_PASSWORD=root
   DB_DATABASE=spinwin
   ```

3. **Iniciar containers:**
   ```bash
   docker-compose up -d
   ```

4. **Verificar se os containers estão rodando:**
   ```bash
   docker-compose ps
   ```

5. **Executar migrations:**
   ```bash
   docker-compose exec php bash -c "php artisan migrate"
   ```

6. **Testar a API:**
   ```bash
   # Testar se a API está funcionando
   curl http://localhost:8080/api/users
   ```

7. **Acessar a API:**
   - Base URL: `http://localhost:8080`
   - API Endpoint: `http://localhost:8080/api`
   - Swagger/OpenAPI: `http://localhost:8080/api/docs` (se disponível)

### Conectar no HeidiSQL

Para acessar o banco de dados via HeidiSQL:

1. **Abra o HeidiSQL**
2. **Clique em "New" (nova sessão)**
3. **Configure a conexão:**
   ```
   Network type: MySQL (TCP/IP)
   Hostname: localhost
   User: root
   Password: root
   Port: 3306
   ```
4. **Clique em "Open"**

**Dados de Conexão:**
- Host: `localhost`
- Port: `3306`
- User: `root`
- Password: `root`
- Database: `spinwin` (será criada automaticamente)

### Opção 2: Local (sem Docker)

1. **Instalar dependências:**
   ```bash
   composer install
   ```

2. **Configurar banco de dados local no `.env`**

3. **Executar migrations:**
   ```bash
   php artisan migrate
   ```

4. **Iniciar o servidor:**
   ```bash
   composer start
   ```
   Ou manualmente:
   ```bash
   php -S localhost:8000 -t public
   ```

5. **Acessar a API:**
   - Base URL: `http://localhost:8000`
   - API Endpoint: `http://localhost:8000/api`

## 🔧 Troubleshooting

### Problemas Comuns

#### 1. Erro de Conexão com MySQL
```
Fatal error: Uncaught mysqli_sql_exception: Connection refused
```

**Solução:** Verifique se as configurações no `.env` estão corretas para Docker:
- `DB_HOST=mysql` (nome do serviço no docker-compose)
- `DB_USERNAME=root`
- `DB_PASSWORD=root`

#### 2. Erro de Timestamp no MySQL 8.0
```
Invalid default value for 'updated_at'
```

**Solução:** Este problema foi corrigido na versão atual. Se persistir, verifique se está usando a versão mais recente do código.

#### 3. Container não inicia
```bash
# Verificar status dos containers
docker-compose ps

# Verificar logs
docker-compose logs

# Reiniciar containers
docker-compose down
docker-compose up -d
```

Para mais detalhes sobre troubleshooting, consulte: [doc/troubleshooting.md](doc/troubleshooting.md)

## 📚 Endpoints da API

### 1. Criar Usuário
**POST** `/api/users`

**Body (JSON):**
```json
{
    "name": "João Silva",
    "email": "joao@exemplo.com",
    "password": "123456"
}
```

**Resposta de Sucesso (201):**
```json
{
    "status": "success",
    "message": "Usuário criado com sucesso!",
    "data": {
        "id": "64f8a1b2c3d4e",
        "name": "João Silva",
        "email": "joao@exemplo.com",
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
    }
}
```

**Resposta de Erro (400):**
```json
{
    "status": "error",
    "message": "Email já está em uso"
}
```

### 2. Listar Usuários
**GET** `/api/users`

**Resposta de Sucesso (200):**
```json
{
    "status": "success",
    "message": "Usuários listados com sucesso!",
    "data": [
        {
            "id": "64f8a1b2c3d4e",
            "name": "João Silva",
            "email": "joao@exemplo.com",
            "created_at": "2024-01-15 10:30:00",
            "updated_at": "2024-01-15 10:30:00"
        }
    ]
}
```

### 3. Buscar Usuário por ID
**GET** `/api/users/{id}`

**Resposta de Sucesso (200):**
```json
{
    "status": "success",
    "message": "Usuário encontrado com sucesso!",
    "data": {
        "id": "64f8a1b2c3d4e",
        "name": "João Silva",
        "email": "joao@exemplo.com",
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
    }
}
```

**Resposta de Erro (404):**
```json
{
    "status": "error",
    "message": "Usuário não encontrado"
}
```

## 🔍 Validações

### Validações de Nome:
- Não pode estar vazio
- Mínimo de 2 caracteres
- Máximo de 100 caracteres

### Validações de Email:
- Não pode estar vazio
- Deve ser um email válido
- Deve ser único (não pode existir outro usuário com o mesmo email)

### Validações de Senha:
- Não pode estar vazia
- Mínimo de 6 caracteres

## 🧪 Testando a API

### Usando cURL:

1. **Criar usuário:**
   ```bash
   curl -X POST http://localhost:8000/api/users \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Maria Santos",
       "email": "maria@exemplo.com",
       "password": "123456"
     }'
   ```

2. **Listar usuários:**
   ```bash
   curl -X GET http://localhost:8000/api/users
   ```

3. **Buscar usuário por ID:**
   ```bash
   curl -X GET http://localhost:8000/api/users/64f8a1b2c3d4e
   ```

### Usando Postman ou Insomnia:

1. Configure a URL base: `http://localhost:8000`
2. Use os endpoints listados acima
3. Para POST, configure o header: `Content-Type: application/json`

## 📁 Estrutura do Projeto

```
spinwin/
├── public/                 # Ponto de entrada
│   ├── index.php
│   └── .htaccess
├── src/
│   ├── Core/              # Regras de negócio
│   │   ├── Entities/
│   │   │   └── User.php
│   │   └── UseCases/
│   │       ├── RegisterUserUseCase.php
│   │       ├── ListUsersUseCase.php
│   │       └── FindUserByIdUseCase.php
│   ├── Application/       # Interfaces
│   │   └── Interfaces/
│   │       └── UserRepositoryInterface.php
│   ├── Infra/            # Infraestrutura
│   │   ├── Http/
│   │   │   ├── Request.php
│   │   │   ├── Response.php
│   │   │   └── UserController.php
│   │   ├── Routes/
│   │   │   └── Router.php
│   │   ├── Persistence/
│   │   │   └── InMemoryUserRepository.php
│   │   └── Database/      # Sistema de migrations
│   │       ├── Migration.php
│   │       ├── Blueprint.php
│   │       ├── MigrationManager.php
│   │       ├── Seeder.php
│   │       ├── SeederManager.php
│   │       └── DatabaseConnection.php
│   └── Shared/           # Código compartilhado
│       └── Response/
│           └── SituacaoEnum.php
├── database/             # Migrations e Seeders
│   ├── migrations/       # Arquivos de migration
│   └── seeders/         # Arquivos de seeder
├── doc/
│   ├── arquitetura.md
│   └── migrations.md
├── artisan              # Comando CLI
├── composer.json
└── README.md
```

## 🔧 Tecnologias Utilizadas

- **PHP 8.0+**
- **Composer** (Gerenciamento de dependências)
- **Clean Architecture** (Arquitetura limpa)
- **PSR-4** (Autoloading)

## 🎯 Benefícios da Arquitetura

1. **Manutenibilidade**: Código organizado e fácil de entender
2. **Testabilidade**: Componentes isolados e testáveis
3. **Flexibilidade**: Fácil troca de implementações
4. **Escalabilidade**: Estrutura preparada para crescimento
5. **Reutilização**: Código compartilhado e bem estruturado

## 🗄️ Sistema de Migrations

O projeto inclui um sistema completo de migrations similar ao Laravel para controle de versão do banco de dados.

### Comandos Disponíveis

```bash
# Migrations
php artisan migrate                    # Executa migrations pendentes
php artisan migrate:status            # Mostra status das migrations
php artisan migrate:rollback          # Reverte última batch
php artisan migrate:reset             # Reverte todas as migrations
php artisan make:migration nome       # Cria nova migration

# Seeders
php artisan seeder:run                # Executa todos os seeders
php artisan seeder:run NomeSeeder     # Executa seeder específico
php artisan seeder:list               # Lista seeders disponíveis
php artisan make:seeder NomeSeeder    # Cria novo seeder
```

### Configuração do Banco

#### 1. Criar arquivo `.env`:
```bash
php setup_env.php
```

Ou crie manualmente o arquivo `.env` na raiz do projeto:

```env
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=spinwin
DB_PORT=3306
```

#### 2. Verificar conexão:
```bash
php check_connection.php
```

#### 3. Solução de problemas:

**Erro de conexão com MariaDB:**
- Use `127.0.0.1` em vez de `localhost`
- Verifique se o MySQL/MariaDB está rodando
- Verifique as credenciais do usuário root

**Erro de sintaxe no .env:**
- Certifique-se de que não há caracteres especiais
- Use o script `setup_env.php` para criar o arquivo

### Documentação Completa

Para mais detalhes sobre o sistema de migrations, consulte: [📖 Documentação das Migrations](doc/migrations.md)

## 🔄 Próximos Passos

- [x] ✅ Sistema de migrations implementado
- [ ] Implementar autenticação JWT
- [ ] Adicionar validação mais robusta
- [ ] Implementar repositório com banco de dados
- [ ] Adicionar testes unitários
- [ ] Implementar logs e monitoramento
- [ ] Adicionar documentação Swagger/OpenAPI

---

**Desenvolvido seguindo os princípios da Clean Architecture** 🏗️ 