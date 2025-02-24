<?php
$host = 'localhost';
$dbname = 'luismi71_destreinados_novo'; // Nome do novo banco
$username = 'luismi71_destreinados_new'; // Novo usuário
$password = 'NovoDestreinados2025!'; // Nova senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'error' => 'Erro de conexão: ' . $e->getMessage()]));
}
?>