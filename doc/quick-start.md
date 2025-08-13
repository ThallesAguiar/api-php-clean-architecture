# Guia de Início Rápido

Este guia irá ajudá-lo a configurar e executar a aplicação em seu ambiente de desenvolvimento.

## Pré-requisitos

- PHP 8.1 ou superior
- Composer

## Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/thallesaguiar/spinwin.git
   cd spinwin
   ```

2. **Instale as dependências:**
   ```bash
   composer install
   ```

3. **Configure o ambiente:**
   - Copie o arquivo `.env.example` para `.env`:
     ```bash
     copy env.example .env
     ```
   - O arquivo `.env` já vem com uma configuração padrão. Você pode alterar a porta da aplicação modificando a variável `PORT`.

## Iniciando a Aplicação

Para iniciar o servidor de desenvolvimento, execute o seguinte comando:

```bash
composer start
```

O servidor será iniciado e a aplicação estará acessível em `http://localhost:8210` (ou na porta que você configurou a variável `NGINX_HOST_PORT` no arquivo `.env`).

**A API estará acessível em `http://localhost:8000/api`**.

## Usando os Comandos Artisan

A aplicação possui um script `artisan` para executar comandos de linha de comando, similar ao Laravel.

### Criando uma Migration

Para criar uma nova migration, utilize o comando `make:migration`:

```bash
php artisan make:migration <nome_da_migration>
```

**Exemplo:**

```bash
php artisan make:migration create_products_table
```

Isso irá criar um novo arquivo de migration na pasta `database/migrations`.