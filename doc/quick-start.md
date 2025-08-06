# 🚀 Quick Start - SpinWin API

Guia rápido para fazer a API funcionar em 5 minutos.

## 📋 Pré-requisitos

- Docker Desktop instalado
- Git instalado
- HeidiSQL (opcional, para visualizar o banco)

## ⚡ Passos Rápidos

### 1. **Clonar e Configurar**
```bash
# Clone o projeto (se ainda não fez)
git clone <url-do-repositorio>
cd spinwin

# Configure o arquivo .env
cp env.example .env
```

### 2. **Configurar .env**
Edite o arquivo `.env` e adicione:

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

### 3. **Iniciar Containers**
```bash
docker-compose up -d
```

### 4. **Verificar Status**
```bash
docker-compose ps
```

Você deve ver:
- `spinwin-mysql` - Up
- `spinwin-php` - Up  
- `spinwin-nginx` - Up

### 5. **Executar Migrations**
```bash
docker-compose exec php bash -c "php artisan migrate"
```

### 6. **Testar a API**
```bash
# Listar usuários
curl http://localhost:8080/api/users

# Criar usuário
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"João Silva","email":"joao@teste.com","password":"123456"}'
```

## 🎯 URLs da API

- **Base URL:** `http://localhost:8080`
- **API Endpoint:** `http://localhost:8080/api`
- **Listar Usuários:** `GET http://localhost:8080/api/users`
- **Criar Usuário:** `POST http://localhost:8080/api/users`
- **Buscar Usuário:** `GET http://localhost:8080/api/users/{id}`

## 🗄️ Conectar no HeidiSQL

1. **Abra o HeidiSQL**
2. **Nova sessão:**
   ```
   Network type: MySQL (TCP/IP)
   Hostname: localhost
   User: root
   Password: root
   Port: 3306
   ```
3. **Clique em "Open"**

## 🔧 Comandos Úteis

```bash
# Ver logs
docker-compose logs

# Reiniciar containers
docker-compose restart

# Parar containers
docker-compose down

# Entrar no container PHP
docker-compose exec php bash

# Verificar status das migrations
docker-compose exec php bash -c "php artisan migrate:status"
```

## 🚨 Problemas Comuns

### API não responde
```bash
# Verificar containers
docker-compose ps

# Ver logs
docker-compose logs nginx
docker-compose logs php
```

### Erro de conexão com banco
```bash
# Testar conexão MySQL
docker-compose exec mysql mysql -u root -proot -e "SHOW DATABASES;"

# Verificar .env
docker-compose exec php bash -c "env | grep DB_"
```

### Migration falha
```bash
# Verificar logs
docker-compose logs php

# Tentar novamente
docker-compose exec php bash -c "php artisan migrate"
```

## ✅ Checklist de Verificação

- [ ] Docker Desktop rodando
- [ ] Containers iniciados (`docker-compose ps`)
- [ ] Arquivo `.env` configurado
- [ ] Migrations executadas
- [ ] API respondendo (`curl http://localhost:8080/api/users`)
- [ ] HeidiSQL conectado (opcional)

## 📞 Suporte

Se algo não funcionar:

1. **Verifique os logs:** `docker-compose logs`
2. **Consulte:** [doc/troubleshooting.md](doc/troubleshooting.md)
3. **Reinicie tudo:** `docker-compose down && docker-compose up -d`

---

**🎉 Pronto! Sua API está funcionando em `http://localhost:8080`** 