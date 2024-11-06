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

// Verificar se a tarefa pertence ao usuário logado
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    echo "Tarefa não encontrada ou você não tem permissão para excluí-la.";
    exit;
}

// Excluir a tarefa e suas associações de tags
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $user_id]);

// Definir mensagem de sucesso
$_SESSION['message'] = 'Tarefa excluída com sucesso!';
$_SESSION['message_type'] = 'success';

// Redirecionar para a página principal
header('Location: index.php');
exit;
