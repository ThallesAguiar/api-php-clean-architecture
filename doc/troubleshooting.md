# Troubleshooting - SpinWin

Este documento contém soluções para problemas comuns encontrados durante o desenvolvimento do projeto SpinWin.

## 🐳 Problemas com Docker

### 1. Erro de Conexão com MySQL

**Sintoma:**
```
Fatal error: Uncaught mysqli_sql_exception: Connection refused
```

**Causa:** Configuração incorreta das variáveis de ambiente para Docker.

**Solução:**
1. Verifique se o arquivo `.env` contém as configurações corretas:

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

2. Reinicie os containers:
```bash
docker-compose down
docker-compose up -d
```

3. Verifique se os containers estão rodando:
```bash
docker-compose ps
```

### 2. Container MySQL não inicia

**Sintoma:**
```
MySQL container keeps restarting
```

**Solução:**
1. Verifique os logs do container MySQL:
```bash
docker-compose logs mysql
```

2. Certifique-se de que a porta 3306 não está sendo usada:
```bash
netstat -an | findstr 3306
```

3. Remova o volume e recrie:
```bash
docker-compose down -v
docker-compose up -d
```

### 3. Permissões de Arquivo

**Sintoma:**
```
Permission denied
```

**Solução:**
1. No Windows, certifique-se de que o Docker Desktop tem acesso ao diretório
2. No Linux/Mac, ajuste as permissões:
```bash
chmod -R 755 .
```

## 🗄️ Problemas com MySQL

### 1. Erro de Timestamp no MySQL 8.0

**Sintoma:**
```
Invalid default value for 'updated_at'
```

**Causa:** MySQL 8.0 não aceita mais o valor `'0000-00-00 00:00:00'` como padrão para TIMESTAMP.

**Solução:** 
O método `timestamps()` na classe `Blueprint` foi corrigido para usar:
```php
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### 2. Erro de Charset

**Sintoma:**
```
Unknown character set
```

**Solução:**
1. Verifique se o MySQL está configurado com UTF-8:
```sql
SHOW VARIABLES LIKE 'character_set%';
```

2. Se necessário, configure no `docker-compose.yml`:
```yaml
mysql:
  command: --default-authentication-plugin=mysql_native_password --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci
```

### 3. Erro de Autenticação

**Sintoma:**
```
Access denied for user
```

**Solução:**
1. Verifique as credenciais no arquivo `.env`
2. Certifique-se de que o usuário existe no MySQL:
```sql
SELECT User, Host FROM mysql.user;
```

## 🔧 Problemas com Migrations

### 1. Migration não encontrada

**Sintoma:**
```
Arquivo de migration não encontrado
```

**Solução:**
1. Verifique se o arquivo existe em `database/migrations/`
2. Verifique se o nome da classe corresponde ao nome do arquivo
3. Certifique-se de que o arquivo tem a extensão `.php`

### 2. Erro de SQL na Migration

**Sintoma:**
```
Erro na migration: SQL syntax error
```

**Solução:**
1. Verifique a sintaxe SQL gerada pelo Blueprint
2. Teste a query diretamente no MySQL
3. Verifique se todos os métodos estão sendo chamados corretamente

### 3. Migration já executada

**Sintoma:**
```
Migration already executed
```

**Solução:**
1. Verifique o status das migrations:
```bash
php artisan migrate:status
```

2. Se necessário, faça rollback:
```bash
php artisan migrate:rollback
```

## 🚀 Problemas com Comandos Artisan

### 1. Comando não encontrado

**Sintoma:**
```
Command not found
```

**Solução:**
1. Verifique se o arquivo `artisan` tem permissão de execução
2. Execute com PHP explicitamente:
```bash
php artisan
```

### 2. Erro de Memória

**Sintoma:**
```
Fatal error: Allowed memory size exhausted
```

**Solução:**
1. Aumente o limite de memória no `php.ini`
2. Ou execute com mais memória:
```bash
php -d memory_limit=512M artisan migrate
```

## 🌐 Problemas com a API

### 1. API não responde

**Sintoma:**
```
Connection refused
```

**Solução:**
1. Verifique se os containers estão rodando:
```bash
docker-compose ps
```

2. Verifique os logs do nginx:
```bash
docker-compose logs nginx
```

3. Verifique os logs do PHP:
```bash
docker-compose logs php
```

### 2. Erro 502 Bad Gateway

**Sintoma:**
```
502 Bad Gateway
```

**Solução:**
1. Verifique se o container PHP está rodando
2. Reinicie os containers:
```bash
docker-compose restart
```

3. Verifique a configuração do nginx:
```bash
docker-compose exec nginx nginx -t
```

### 3. API retorna erro 500

**Sintoma:**
```
500 Internal Server Error
```

**Solução:**
1. Verifique os logs do PHP:
```bash
docker-compose logs php
```

2. Verifique se o banco está conectado:
```bash
docker-compose exec php bash -c "php artisan migrate:status"
```

3. Teste a conexão com o banco:
```bash
docker-compose exec mysql mysql -u root -proot -e "SHOW DATABASES;"
```

### 4. CORS Issues

**Sintoma:**
```
CORS error in browser
```

**Solução:**
1. Verifique se o nginx está configurado para CORS
2. Adicione headers CORS no nginx se necessário
3. Use um proxy ou configure o frontend adequadamente

## 🔍 Debug e Logs

### 1. Habilitar Logs Detalhados

Adicione logs nas migrations para debug:

```php
public function up(): void
{
    echo "Executando migration: " . get_class($this) . "\n";
    
    try {
        $this->createTable('users', function($table) {
            $table->id();
            $table->string('name');
        });
        
        echo "Migration executada com sucesso!\n";
    } catch (Exception $e) {
        echo "Erro na migration: " . $e->getMessage() . "\n";
        throw $e;
    }
}
```

### 2. Verificar Conexão com Banco

Crie um script de teste:

```php
<?php
// test_connection.php
require_once 'vendor/autoload.php';

use App\Infra\Database\DatabaseConnection;

try {
    $connection = DatabaseConnection::getInstance();
    echo "Conexão estabelecida com sucesso!\n";
} catch (Exception $e) {
    echo "Erro de conexão: " . $e->getMessage() . "\n";
}
```

### 3. Verificar Configurações

```bash
# Verificar variáveis de ambiente
docker-compose exec php bash -c "env | grep DB_"

# Verificar conexão MySQL
docker-compose exec mysql mysql -u root -proot -e "SHOW DATABASES;"
```

## 📋 Checklist de Solução de Problemas

### Para Problemas de Conexão:
- [ ] Verificar se os containers estão rodando
- [ ] Verificar configurações no `.env`
- [ ] Verificar logs dos containers
- [ ] Testar conexão manualmente

### Para Problemas de Migration:
- [ ] Verificar se o arquivo existe
- [ ] Verificar sintaxe da migration
- [ ] Verificar status das migrations
- [ ] Verificar logs de erro

### Para Problemas de Docker:
- [ ] Verificar se o Docker está rodando
- [ ] Verificar se as portas estão livres
- [ ] Verificar permissões de arquivo
- [ ] Reiniciar containers se necessário

## 🆘 Comandos Úteis

```bash
# Docker
docker-compose ps
docker-compose logs
docker-compose down
docker-compose up -d

# Migrations
php artisan migrate:status
php artisan migrate:rollback
php artisan migrate:reset

# API e Debug
docker-compose exec php bash
docker-compose exec mysql bash

# Testar API
curl http://localhost:8080/api/users
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"Teste","email":"teste@teste.com","password":"123456"}'

# Verificar logs da API
docker-compose logs nginx
docker-compose logs php
```

---

**Nota:** Se o problema persistir, verifique os logs detalhados e consulte a documentação oficial do Docker e MySQL. 