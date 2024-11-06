<?php
// Configuração de conexão com o banco de dados
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root'; // Substitua pelo seu usuário do MySQL
$password = '';     // Substitua pela sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
