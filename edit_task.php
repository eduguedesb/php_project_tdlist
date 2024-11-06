<?php
session_start();
require 'db.php'; // Arquivo de conexão com o banco de dados

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    echo "Tarefa não encontrada.";
    exit;
}

// Buscar os dados da tarefa
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    echo "Tarefa não encontrada ou você não tem permissão para editá-la.";
    exit;
}

// Processar a atualização da tarefa ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $tags = $_POST['tags'] ?? []; // Array de tags selecionadas

    // Atualizar a tarefa na tabela `tasks`
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $description, $status, $task_id, $user_id]);

    // Atualizar as tags associadas à tarefa
    $pdo->prepare("DELETE FROM task_tags WHERE task_id = ?")->execute([$task_id]);
    foreach ($tags as $tag_id) {
        $pdo->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)")
            ->execute([$task_id, $tag_id]);
    }

    // Redirecionar com mensagem de sucesso
    $_SESSION['message'] = 'Tarefa atualizada com sucesso!';
    $_SESSION['message_type'] = 'success';
    header('Location: index.php');
    exit;
}

// Buscar todas as tags para exibir no formulário
$tags = $pdo->query("SELECT * FROM tags")->fetchAll();

// Buscar tags associadas à tarefa atual
$taskTags = $pdo->prepare("SELECT tag_id FROM task_tags WHERE task_id = ?");
$taskTags->execute([$task_id]);
$taskTags = $taskTags->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Editar Tarefa</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Editar Tarefa</h2>
    <form action="update_task.php?id=<?= htmlspecialchars($task_id) ?>" method="POST">
        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Concluída</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Tags</label>
            <?php foreach ($tags as $tag): ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="tags[]" value="<?= $tag['id'] ?>" 
                        <?= in_array($tag['id'], $taskTags) ? 'checked' : '' ?>>
                    <label class="form-check-label"><?= htmlspecialchars($tag['name']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
