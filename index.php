<?php
session_start();
require 'db.php'; // Arquivo de conexão com o banco de dados

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$statusFilter = $_GET['status'] ?? 'all';
$tagFilter = $_GET['tag'] ?? 'all';

// Configuração da paginação
$tasksPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $tasksPerPage;

// Construir a consulta SQL com filtros
$query = "SELECT t.* FROM tasks t WHERE t.user_id = ?";
$params = [$user_id];

if ($statusFilter !== 'all') {
    $query .= " AND t.status = ?";
    $params[] = $statusFilter;
}

if ($tagFilter !== 'all') {
    $query .= " AND t.id IN (SELECT tt.task_id FROM task_tags tt WHERE tt.tag_id = ?)";
    $params[] = $tagFilter;
}

$query .= " LIMIT ? OFFSET ?";
$params[] = $tasksPerPage;
$params[] = $offset;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

// Obter o total de tarefas para a paginação
$totalTasksQuery = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$totalTasksQuery->execute([$user_id]);
$totalTasks = $totalTasksQuery->fetchColumn();
$totalPages = ceil($totalTasks / $tasksPerPage);

// Obter todas as tags para o filtro e seleção
$tags = $pdo->query("SELECT * FROM tags")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gerenciador de Tarefas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

    <h2>Gerenciador de Tarefas</h2>

    <!-- Exibir mensagens de sucesso ou erro -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Formulário de Filtro -->
    <form method="GET" action="index.php" class="form-inline mb-3">
        <select name="tag" class="form-control mr-2">
            <option value="all">Todas as Tags</option>
            <?php foreach ($tags as $tag): ?>
                <option value="<?= $tag['id'] ?>" <?= $tagFilter == $tag['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tag['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="form-control mr-2">
            <option value="all">Todos</option>
            <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pendentes</option>
            <option value="completed" <?= $statusFilter == 'completed' ? 'selected' : '' ?>>Concluídas</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
        <a href="create_task.php" class="btn btn-primary ml-2">Nova Tarefa</a>
    </form>

    <!-- Exibição das Tarefas -->
    <div class="row">
        <?php foreach ($tasks as $task): ?>
            <div class="col-md-4 mb-3">
                <div class="card <?= $task['status'] === 'completed' ? 'border-success' : '' ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($task['title']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($task['description']) ?></p>
                        <p class="card-text"><small class="text-muted">Status: <?= $task['status'] === 'completed' ? 'Concluída' : 'Pendente' ?></small></p>
                        <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="delete_task.php?id=<?= $task['id'] ?>" onclick="return confirm('Tem certeza de que deseja excluir esta tarefa? Esta ação não pode ser desfeita.')" class="btn btn-danger btn-sm">Excluir</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <li class="page-item <?= $page == $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="index.php?page=<?= $page ?>&status=<?= $statusFilter ?>&tag=<?= $tagFilter ?>"><?= $page ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Scripts do Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
