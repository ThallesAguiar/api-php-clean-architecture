# Docker - Ambiente de Desenvolvimento SpinWin

Este documento explica como utilizar o ambiente Docker para rodar o projeto SpinWin com PHP, MySQL e Nginx.

---

## 📦 O que está incluído?

- **MySQL 8.0** (persistência de dados)
- **PHP 8.2-FPM** (com extensões mysqli, pdo, pdo_mysql)
- **Nginx** (servidor web)
- Volumes para persistência e código

---

## 🗂️ Estrutura dos arquivos

```
spinwin/
├── .docker/
│   └── mysql.env           # Variáveis de ambiente do MySQL
├── nginx/
│   └── default.conf        # Configuração do Nginx
├── Dockerfile              # Imagem customizada do PHP
├── docker-compose.yml      # Orquestração dos containers
└── ...
```

---

## ⚙️ Configuração do Banco de Dados

No seu arquivo `.env` do projeto, use:

```
DB_HOST=mysql
DB_DATABASE=spinwin
DB_USERNAME=spinwin
DB_PASSWORD=spinwin
DB_PORT=3306
```

Esses valores são definidos em `.docker/mysql.env`.

---

## 🚀 Como subir o ambiente

1. **Suba os containers:**
   ```bash
   docker-compose up -d
   ```

2. **Acesse a aplicação:**
   - http://localhost:8080

3. **Acesse o MySQL:**
   - Host: `localhost`
   - Porta: `3306`
   - Usuário: `spinwin`
   - Senha: `spinwin`
   - Banco: `spinwin`

4. **Acesse o terminal do PHP:**
   ```bash
   docker-compose exec php bash
   ```

5. **Rode comandos artisan dentro do container:**
   ```bash
   php artisan migrate
   php artisan seeder:run
   ```

---

## 📝 Arquivos principais

### docker-compose.yml
Orquestra os serviços (MySQL, PHP, Nginx) e define volumes e redes.

### Dockerfile
Define a imagem do PHP com as extensões necessárias.

### nginx/default.conf
Configuração do Nginx para servir a aplicação PHP na pasta `public`.

### .docker/mysql.env
Variáveis de ambiente do banco de dados MySQL.

---

## 🛠️ Dicas úteis

- Para reiniciar o ambiente:
  ```bash
  docker-compose down
  docker-compose up -d
  ```
- Para ver os logs:
  ```bash
  docker-compose logs -f
  ```
- Para acessar o MySQL via terminal:
  ```bash
  docker-compose exec mysql mysql -u spinwin -p
  # senha: spinwin
  ```
- Para instalar dependências PHP:
  ```bash
  docker-compose exec php composer install
  ```

---

## 🧹 Limpando o ambiente

- Para remover todos os containers, volumes e redes:
  ```bash
  docker-compose down -v
  ```

---

## 🐳 Observações

- O volume `db_data` garante que os dados do banco não serão perdidos ao reiniciar os containers.
- O código-fonte é montado como volume, então alterações no host refletem imediatamente no container.
- O Nginx serve a aplicação a partir da pasta `public`.
- O PHP roda como FPM e se comunica com o Nginx via socket de rede interna.

---

## 📚 Referências
- [Documentação oficial do Docker](https://docs.docker.com/)
- [Documentação do Docker Compose](https://docs.docker.com/compose/)
- [Documentação do MySQL Docker](https://hub.docker.com/_/mysql)
- [Documentação do PHP Docker](https://hub.docker.com/_/php)
- [Documentação do Nginx Docker](https://hub.docker.com/_/nginx) 