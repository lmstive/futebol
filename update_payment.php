<?php
header('Content-Type: application/json');
require 'config.php';
session_start();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit;
}

$player = $_POST['player'] ?? '';
$status = $_POST['status'] ?? '';

if ($player && in_array($status, ['OK', 'Pendente'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO payments (player, status, last_reset) VALUES (?, ?, CURDATE()) ON DUPLICATE KEY UPDATE status = ?, last_reset = CURDATE()");
        $success = $stmt->execute([$player, $status, $status]);
        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
exit;
?>