# Sistema de Tarefas Multiempresa

Sistema completo de gerenciamento de tarefas com suporte a múltiplas empresas (multitenancy), autenticação JWT e comunicação via API REST.

## Stack Técnica

### Backend

- Laravel 11
- PHP 8.2
- MySQL 8.0
- Redis (filas e cache)
- JWT Authentication (php-open-source-saver/jwt-auth)
- OpenSpout (exportação Excel)
- Pest (testes)

### Frontend

- Vue 3 (Composition API)
- Vite
- Tailwind CSS
- Pinia (state management)
- Vue Router
- Axios

### Infraestrutura

- Docker Compose
- Nginx
- MailHog (desenvolvimento)

## Recursos Implementados

### Obrigatórios

- ✅ Autenticação JWT completa (registro, login, logout)
- ✅ Sistema de multitenancy com isolamento de dados
- ✅ CRUD completo de tarefas
- ✅ Validações em todos os formulários
- ✅ Filtros por status e prioridade
- ✅ Frontend Vue.js com interface completa
- ✅ Envio de emails ao criar e completar tarefas

### Bônus (Todos Implementados)

- ✅ Comando interativo para criar empresa e usuário inicial
- ✅ Filas com Redis para processamento assíncrono
- ✅ Emails enviados via filas
- ✅ Exportação de tarefas em Excel (assíncrona)
- ✅ Docker completo para todo o ambiente

## Instalação e Execução

### Pré-requisitos

- Docker e Docker Compose
- Git

### 1. Clone o Repositório

```bash
git clone <repository-url>
cd toDoListMultitenancy
```

### 2. Inicie os Containers Docker

```bash
docker-compose up -d
```

Isso irá iniciar:

- MySQL (porta 3306)
- Redis (porta 6379)
- MailHog (porta 8025 para interface web)
- Backend Laravel (porta 8000)
- Frontend Vue (porta 5173)
- Queue Worker (processamento de filas)

### 3. Instale as Dependências do Backend

```bash
docker-compose exec app composer install
```

### 4. Configure o Ambiente

```bash
cp backend/.env.example backend/.env
docker-compose restart app queue
```

### 5. Configure o Backend

```bash
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan jwt:secret --force
docker-compose exec app php artisan migrate
```

### 6. Crie a Primeira Empresa e Usuário

```bash
docker-compose exec app php artisan tenant:setup
```

Siga as instruções interativas para criar:

- Nome da empresa
- Slug da empresa (identificador único)
- Nome do usuário administrador
- Email do usuário
- Senha do usuário

### 7. Inicie o Frontend

```bash
cd frontend
npm install
npm run dev
```

### 8. Acesse a Aplicação

- **Frontend**: http://localhost:5173
- **API Backend**: http://localhost:8000
- **MailHog (emails)**: http://localhost:8025

### Credenciais de Acesso

Use as credenciais criadas no passo 5 para fazer login.

## Estrutura do Projeto

```
toDoListMultitenancy/
├── backend/              # Laravel 11
│   ├── app/
│   │   ├── Console/      # Comandos Artisan
│   │   ├── Exports/      # Classes de exportação
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   ├── Middleware/
│   │   │   └── Requests/
│   │   ├── Jobs/         # Jobs de filas
│   │   ├── Mail/         # Classes de email
│   │   ├── Models/
│   │   └── Traits/       # Trait de multitenancy
│   ├── database/
│   │   ├── factories/
│   │   └── migrations/
│   ├── routes/
│   └── tests/            # Testes Pest
├── frontend/             # Vue 3
│   ├── src/
│   │   ├── components/
│   │   ├── composables/
│   │   ├── router/
│   │   ├── services/
│   │   ├── stores/
│   │   └── views/
│   └── package.json
├── docker/               # Configurações Docker
│   ├── nginx/
│   └── php/
└── docker-compose.yml
```

## API Endpoints

### Autenticação

- `POST /api/auth/register` - Registrar novo usuário
- `POST /api/auth/login` - Login e obter token JWT
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Dados do usuário autenticado

### Tarefas

- `GET /api/tasks` - Listar tarefas (com filtros)
- `POST /api/tasks` - Criar tarefa
- `GET /api/tasks/{id}` - Ver detalhes da tarefa
- `PUT /api/tasks/{id}` - Atualizar tarefa
- `DELETE /api/tasks/{id}` - Deletar tarefa

### Exportação

- `POST /api/exports/tasks` - Solicitar exportação
- `GET /api/exports/tasks/{filename}/status` - Verificar status
- `GET /api/exports/tasks/{filename}/download` - Download do arquivo

## Filtros Disponíveis

Na listagem de tarefas, você pode usar os seguintes filtros via query string:

- `status` - pending, in_progress, completed
- `priority` - low, medium, high
- `search` - Busca em título e descrição
- `due_date_from` - Data inicial (YYYY-MM-DD)
- `due_date_to` - Data final (YYYY-MM-DD)
- `user_id` - ID do usuário

Exemplo: `/api/tasks?status=pending&priority=high&search=importante`

## Testes

### Executar Todos os Testes

```bash
docker-compose exec app php artisan test
```

### Executar Testes Específicos

```bash
docker-compose exec app php artisan test --testsuite=Feature
docker-compose exec app php artisan test --testsuite=Unit
docker-compose exec app php artisan test tests/Feature/AuthTest.php
```

### Cobertura de Testes

- ✅ Autenticação JWT
- ✅ CRUD de tarefas
- ✅ Isolamento multitenancy
- ✅ Filtros e buscas
- ✅ Exportação assíncrona
- ✅ Relacionamentos de models
- ✅ Validações

Total: 53 testes unitários

## Multitenancy

O sistema implementa multitenancy com as seguintes características:

- **Isolamento de Dados**: Cada empresa tem seus próprios dados completamente isolados
- **Global Scope**: Aplicado automaticamente em todos os models
- **Middleware**: Valida tenant_id em todas as requisições
- **Auto-preenchimento**: tenant_id é preenchido automaticamente ao criar registros
- **JWT Claims**: Token JWT inclui tenant_id para identificação

## Filas e Jobs

O sistema usa Redis para gerenciar filas assíncronas:

- **SendTaskCreatedEmail**: Envia email quando uma tarefa é criada
- **SendTaskCompletedEmail**: Envia email quando uma tarefa é completada
- **GenerateTasksExport**: Gera arquivo Excel com as tarefas

O worker de filas está rodando automaticamente no Docker.

## Emails

Emails são enviados via MailHog em desenvolvimento:

- Acesse http://localhost:8025 para ver os emails
- Em produção, configure SMTP no `.env`

## Desenvolvimento

### Comandos Úteis

```bash
# Logs da aplicação
docker-compose logs -f app

# Logs do worker de filas
docker-compose logs -f queue

# Acessar bash do container
docker-compose exec app bash

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Recriar banco de dados
docker-compose exec app php artisan migrate:fresh
```

## Decisões

Optei por **Single Database** com coluna `tenant_id` por ser:

- Mais simples de implementar e manter
- Eficiente para volume moderado de dados
- Facilita backups e manutenção
- Boa performance com índices corretos

### JWT vs Sanctum

Escolhi JWT porque:

- Requisito explícito do teste técnico
- Stateless (não requer sessões)
- Ideal para SPAs
- Token inclui claims customizados (tenant_id)

### OpenSpout vs PhpSpreadsheet

Optei por OpenSpout porque:

- Melhor performance com grandes volumes
- Menor uso de memória
- Suporta processamento em chunks
