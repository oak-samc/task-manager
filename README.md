# Gerenciador de Tarefas

Uma aplicação SPA (Single Page Application) para gerenciar projetos e suas respectivas tarefas, desenvolvida como parte de um desafio técnico.

## Funcionalidades Implementadas

### Backend (Laravel)

- **API RESTful completa** para projetos e tarefas
- **Banco de dados** com migrations para tabelas `projects` e `tasks`
- **Models Eloquent** com relacionamentos (Project hasMany Tasks, Task belongsTo Project)
- **Validações** usando Form Requests para integridade dos dados
- **Relacionamento 1:N** entre projetos e tarefas

### Frontend (Vue.js)

- **Listagem de projetos** na barra lateral
- **Seleção de projeto** para visualizar tarefas específicas
- **Visualização Kanban** com três colunas: "Pendente", "Em Andamento" e "Concluído"
- **Criar tarefas** no projeto selecionado (status inicial "Pendente")
- **Mover tarefas** entre colunas via drag-and-drop
- **Trocar posições** entre tarefas na mesma coluna
- **Excluir tarefas** com confirmação
- **Editar tarefas** inline
- **Interface responsiva**

## Tecnologias Utilizadas

**Backend:**

- Laravel 11
- SQLite (desenvolvimento)
- PHP 8.2+

**Frontend:**

- Vue.js 3
- Vue Router
- Axios
- CSS puro

## Instruções de Instalação

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js 20.19.0 ou superior
- npm

### 1. Clonar o repositório

```bash
git clone https://github.com/seu-usuario/task-manager.git
cd task-manager
```

> **Nota:** Substitua `seu-usuario` pela URL real do repositório

### 2. Configurar o Backend

```bash
cd backend

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Criar banco de dados SQLite
touch database/database.sqlite
# No Windows, use: type nul > database/database.sqlite

# Executar migrations
php artisan migrate

# Iniciar servidor
php artisan serve
```

### 3. Configurar o Frontend

```bash
cd frontend

# Instalar dependências
npm install

# Iniciar servidor de desenvolvimento
npm run dev
```

### 4. Acessar a Aplicação

Após seguir os passos acima, você terá:

- **Backend:** http://localhost:8000 (API Laravel)
- **Frontend:** http://localhost:5173 (Interface Vue.js)

> **Importante:** Mantenha ambos os servidores rodando em terminais separados para o funcionamento completo da aplicação.

## Endpoints da API

### Projetos

- `GET /api/projects` - Lista todos os projetos
- `POST /api/projects` - Cria um novo projeto
- `GET /api/projects/{id}` - Mostra um projeto específico
- `DELETE /api/projects/{id}` - Remove um projeto
- `GET /api/projects/{id}/tasks` - Lista tarefas de um projeto

### Tarefas

- `GET /api/tasks` - Lista todas as tarefas
- `POST /api/tasks` - Cria uma nova tarefa
- `GET /api/tasks/{id}` - Mostra uma tarefa específica
- `PUT /api/tasks/{id}` - Atualiza uma tarefa
- `DELETE /api/tasks/{id}` - Remove uma tarefa

## Validações Implementadas

### Project

- `name`: obrigatório e único

### Task

- `title`: obrigatório
- `project_id`: obrigatório
- `description`: opcional
- `status`: obrigatório (pendente, em_andamento, concluido)

## Respostas de Reflexão

### Pergunta 1: Qual foi a maior dificuldade que você encontrou neste desafio e como você a resolveu?

A parte mais complicada foi fazer o drag-and-drop funcionar direito, principalmente quando queria que as tarefas trocassem de lugar na mesma coluna. No começo eu só conseguia mover entre colunas diferentes, mas trocar a ordem dentro da mesma coluna estava dando problema.

Tive que estudar bastante sobre os eventos de drag do HTML5 e descobri que precisava controlar melhor quando uma tarefa estava sendo arrastada sobre outra. Acabei criando variáveis para guardar qual tarefa estava sendo arrastada e sobre qual ela estava passando, e aí consegui fazer a lógica para trocar as posições no array.

### Pergunta 2: Se você tivesse mais 4 horas para trabalhar neste projeto, o que você melhoraria ou adicionaria?

1. **Testes**: Eu sei que é importante ter testes, mas acabei focando mais em fazer funcionar primeiro. Gostaria de aprender a escrever testes para o Laravel e para o Vue também.
2. **Melhorar a interface**: Colocaria umas animações quando as tarefas se movem, e talvez uns loading spinners quando está salvando. Também queria melhorar como funciona no celular.
3. **Mais funcionalidades**: Seria legal poder editar o nome dos projetos, colocar prioridades nas tarefas, e talvez um sistema de busca quando tiver muitas tarefas.

### Pergunta 3: Qual abordagem você usou para gerenciar o estado no Vue e por que você escolheu essa abordagem?

Eu usei o estado local dos componentes mesmo, sem Pinia ou Vuex. Pensei em usar o Pinia no começo, mas como o projeto não é muito grande, achei que iria complicar demais.

O que eu fiz foi:

- Cada componente cuida do seu próprio estado
- O projeto selecionado fica na URL, então se a pessoa recarregar a página não perde onde estava
- Os dados das tarefas ficam no componente TasksView e são atualizados quando precisa

Acho que para um projeto desse tamanho funcionou bem, mas se fosse crescer muito eu provavelmente usaria o Pinia para organizar melhor os dados.

## Estrutura do Projeto

```
task-manager/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Http/Requests/
│   │   └── Models/
│   ├── database/migrations/
│   └── routes/api.php
└── frontend/
    ├── src/
    │   ├── views/
    │   ├── services/
    │   └── router/
    └── package.json
```

## Commits e Versionamento

O projeto foi desenvolvido com commits pequenos e mensagens claras, seguindo boas práticas de versionamento Git para facilitar o acompanhamento do desenvolvimento.
