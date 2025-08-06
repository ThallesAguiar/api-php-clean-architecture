# Sistema de Migrations - SpinWin

Este documento descreve o sistema de migrations implementado no projeto SpinWin, similar ao Laravel, para controle de versão do banco de dados.

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Instalação e Configuração](#instalação-e-configuração)
3. [Comandos Disponíveis](#comandos-disponíveis)
4. [Criando Migrations](#criando-migrations)
5. [Criando Seeders](#criando-seeders)
6. [Estrutura das Classes](#estrutura-das-classes)
7. [Exemplos Práticos](#exemplos-práticos)
8. [Troubleshooting](#troubleshooting)

## 🎯 Visão Geral

O sistema de migrations permite:
- ✅ Controle de versão do banco de dados
- ✅ Rollback de alterações
- ✅ Suporte a seeders para dados iniciais
- ✅ Controle de batches de migrations
- ✅ Interface similar ao Laravel

## ⚙️ Instalação e Configuração

### 1. Configuração com Docker

O projeto usa Docker para desenvolvimento. Para configurar:

#### 1.1. Arquivo `.env`
Crie um arquivo `.env` na raiz do projeto com as configurações para Docker:

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

#### 1.2. Executar Containers
```bash
# Iniciar os containers
docker-compose up -d

# Verificar status
docker-compose ps

# Entrar no container PHP
docker-compose exec php bash
```

#### 1.3. Executar Migrations no Docker
```bash
# Executar migrations
docker-compose exec php bash -c "php artisan migrate"

# Verificar status
docker-compose exec php bash -c "php artisan migrate:status"
```

### 2. Configuração Local (sem Docker)

Crie um arquivo `.env` na raiz do projeto:

```env
# Configurações do Banco de Dados
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=spinwin
DB_PORT=3306
```

### 2. Estrutura de Diretórios

```
spinwin/
├── database/
│   ├── migrations/     # Arquivos de migration
│   └── seeders/       # Arquivos de seeder
├── src/Infra/Database/
│   ├── Migration.php
│   ├── Blueprint.php
│   ├── MigrationManager.php
│   ├── Seeder.php
│   ├── SeederManager.php
│   └── DatabaseConnection.php
└── artisan            # Comando CLI
```

## 🚀 Comandos Disponíveis

### Migrations

```bash
# Executa migrations pendentes
php artisan migrate

# Mostra status das migrations
php artisan migrate:status

# Reverte última batch de migrations
php artisan migrate:rollback

# Reverte todas as migrations
php artisan migrate:reset

# Cria nova migration
php artisan make:migration nome_da_migration
```

### Seeders

```bash
# Executa todos os seeders
php artisan seeder:run

# Executa seeder específico
php artisan seeder:run NomeSeeder

# Lista seeders disponíveis
php artisan seeder:list

# Cria novo seeder
php artisan make:seeder NomeSeeder
```

## 📝 Criando Migrations

### 1. Criando uma Migration

```bash
php artisan make:migration create_users_table
```

Isso criará um arquivo como: `2024_01_01_123456_create_users_table.php`

### 2. Estrutura de uma Migration

```php
<?php

use App\Infra\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->createTable('users', function($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->dropTable('users');
    }
}
```

### 3. Métodos Disponíveis no Blueprint

#### Colunas Básicas
```php
$table->id();                    // INT AUTO_INCREMENT PRIMARY KEY
$table->string('name');          // VARCHAR(255)
$table->string('email', 100);    // VARCHAR(100)
$table->text('description');     // TEXT
$table->integer('age');          // INT
$table->bigInteger('count');     // BIGINT
$table->decimal('price', 8, 2);  // DECIMAL(8,2)
$table->boolean('active');       // BOOLEAN
$table->timestamp('created_at'); // TIMESTAMP
```

#### Modificadores
```php
$table->string('email')->unique();           // UNIQUE
$table->string('name')->nullable();          // NULL
$table->boolean('active')->default(true);    // DEFAULT
$table->string('email')->index();           // INDEX
```

#### Timestamps
```php
$table->timestamps();  // created_at e updated_at
```

#### Chaves Estrangeiras
```php
$table->foreignId('user_id')->references('id')->on('users');
```

## 🌱 Criando Seeders

### 1. Criando um Seeder

```bash
php artisan make:seeder UserSeeder
```

### 2. Estrutura de um Seeder

```php
<?php

use App\Infra\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa a tabela
        $this->truncate('users');
        
        // Insere dados
        $this->insert('users', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT)
        ]);
        
        // Insere múltiplos registros
        $this->insertMultiple('users', [
            [
                'name' => 'Maria Santos',
                'email' => 'maria@example.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT)
            ],
            [
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@example.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT)
            ]
        ]);
    }
}
```

### 3. Métodos Disponíveis no Seeder

```php
$this->insert('table', $data);           // Insere um registro
$this->insertMultiple('table', $data);   // Insere múltiplos registros
$this->truncate('table');                // Limpa a tabela
$this->delete('table', 'where = 1');     // Deleta registros
$this->execute('SQL');                   // Executa SQL customizado
```

## 🏗️ Estrutura das Classes

### Migration.php
Classe base para todas as migrations. Fornece métodos para:
- `up()`: Executa a migration
- `down()`: Reverte a migration
- `createTable()`: Cria tabelas
- `dropTable()`: Remove tabelas

### Blueprint.php
Classe para definir a estrutura das tabelas. Fornece métodos fluentes para:
- Definir colunas
- Adicionar índices
- Configurar chaves estrangeiras
- Gerar SQL automaticamente

### MigrationManager.php
Gerencia a execução das migrations:
- Controla batches
- Registra migrations executadas
- Permite rollback
- Mostra status

### Seeder.php
Classe base para seeders. Fornece métodos para:
- Inserir dados
- Limpar tabelas
- Executar SQL customizado

### DatabaseConnection.php
Gerencia a conexão com o banco:
- Singleton pattern
- Configuração via .env
- Métodos utilitários

## 📚 Exemplos Práticos

### Exemplo 1: Tabela de Produtos

```php
// Migration
class CreateProductsTable extends Migration
{
    public function up(): void
    {
        $this->createTable('products', function($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('active')->default(true);
            $table->foreignId('category_id')->references('id')->on('categories');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->dropTable('products');
    }
}

// Seeder
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->insertMultiple('products', [
            [
                'name' => 'Notebook Dell',
                'description' => 'Notebook Dell Inspiron 15',
                'price' => 2999.99,
                'stock' => 10,
                'category_id' => 1
            ],
            [
                'name' => 'Mouse Logitech',
                'description' => 'Mouse sem fio Logitech',
                'price' => 89.90,
                'stock' => 50,
                'category_id' => 2
            ]
        ]);
    }
}
```

### Exemplo 2: Tabela de Pedidos

```php
class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        $this->createTable('orders', function($table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->dropTable('orders');
    }
}
```

## 🔧 Troubleshooting

### Problemas Comuns

#### 1. Erro de Conexão com Docker
```
Fatal error: Uncaught mysqli_sql_exception: Connection refused
```

**Causa:** Configuração incorreta das variáveis de ambiente para Docker.

**Solução:** 
1. Verifique se o arquivo `.env` contém as configurações corretas para Docker:

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

**Importante:** 
- `DB_HOST=mysql` (nome do serviço no docker-compose)
- `DB_USERNAME=root` (usuário root do MySQL)
- `DB_PASSWORD=root` (senha definida em MYSQL_ROOT_PASSWORD)

#### 2. Erro de Timestamp no MySQL 8.0
```
Invalid default value for 'updated_at'
```

**Causa:** MySQL 8.0 não aceita mais o valor `'0000-00-00 00:00:00'` como padrão para TIMESTAMP.

**Solução:** O método `timestamps()` foi corrigido para usar:
```php
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

#### 3. Erro de Conexão Geral
```
Erro: Erro ao conectar com o banco de dados
```
**Solução:** Verifique as configurações no arquivo `.env`

#### 2. Migration não encontrada
```
Erro: Arquivo de migration não encontrado
```
**Solução:** Verifique se o arquivo existe em `database/migrations/`

#### 3. Classe não encontrada
```
Erro: Classe de migration não encontrada
```
**Solução:** Verifique se o nome da classe corresponde ao nome do arquivo

#### 4. Erro de SQL
```
Erro: Erro na migration
```
**Solução:** Verifique a sintaxe SQL gerada pelo Blueprint

### Dicas de Desenvolvimento

1. **Sempre implemente o método `down()`** para permitir rollback
2. **Use transações** para operações complexas
3. **Teste as migrations** em ambiente de desenvolvimento
4. **Mantenha backups** antes de executar migrations em produção
5. **Documente mudanças** importantes nas migrations

### Logs e Debug

Para debug, você pode adicionar logs nas migrations:

```php
public function up(): void
{
    echo "Executando migration: " . get_class($this) . "\n";
    
    $this->createTable('users', function($table) {
        $table->id();
        $table->string('name');
    });
    
    echo "Migration executada com sucesso!\n";
}
```

## 📖 Comandos de Referência Rápida

```bash
# Desenvolvimento
php artisan make:migration create_table_name
php artisan migrate
php artisan migrate:status

# Rollback
php artisan migrate:rollback
php artisan migrate:reset

# Seeders
php artisan make:seeder SeederName
php artisan seeder:run
php artisan seeder:run SeederName

# Ajuda
php artisan
```

---

**Nota:** Este sistema foi inspirado no Laravel e adaptado para PHP puro com MySQLi. Para suporte ou dúvidas, consulte a documentação ou abra uma issue no repositório. 