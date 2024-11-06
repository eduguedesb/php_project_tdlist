<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $tags = $_POST['tags'] ?? [];

    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $title, $description]);
    $task_id = $pdo->lastInsertId();

    foreach ($tags as $tag_id) {
        $pdo->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)")
            ->execute([$task_id, $tag_id]);
    }

    $_SESSION['message'] = 'Tarefa criada com sucesso!';
    $_SESSION['message_type'] = 'success';
    header('Location: index.php');
    exit;
}

$tags = $pdo->query("SELECT * FROM tags")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Tarefa</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Criar Tarefa</h2>
    <form action="create_task.php" method="POST">
        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label>Tags</label>
            <?php foreach ($tags as $tag): ?>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="tags[]" value="<?= $tag['id'] ?>">
                    <label class="form-check-label"><?= htmlspecialchars($tag['name']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary">Criar Tarefa</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
