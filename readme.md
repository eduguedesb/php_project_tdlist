Este é um sistema simples de gerenciamento de tarefas (To-Do List) desenvolvido em PHP, onde os usuários podem criar, atualizar, marcar como concluídas, excluir tarefas e adicionar tags para categorizar as tarefas. O sistema inclui funcionalidades de autenticação, filtros de status e tags, e paginação.

Funcionalidades

Autenticação: Registro, login e logout de usuários.
CRUD de Tarefas: Criar, visualizar, atualizar e excluir tarefas.
Tags: Associar múltiplas tags para categorizar as tarefas.
Filtros: Filtrar tarefas por status (pendente ou concluída) e tags.
Paginação: Navegar entre as tarefas com paginação.

Tecnologias Utilizadas

Backend: PHP 7+ com PDO para interação com o MySQL.
Banco de Dados: MySQL.
Frontend: Bootstrap 4 para estilização básica e responsividade.

Estrutura do Projeto

db.php – Conexão com o banco de dados.
register.php – Página de registro de usuários.
login.php – Página de login de usuários.
logout.php – Página de logout para encerrar a sessão.
index.php – Página principal para exibir as tarefas, com filtros e paginação.
create_task.php – Página para criação de uma nova tarefa.
edit_task.php – Página para edição de uma tarefa existente.
delete_task.php – Script para exclusão de uma tarefa.
