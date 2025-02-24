<?php
header('Content-Type: application/json');
require 'config.php';
session_start();

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit;
}

$location = $_POST['location'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';

if ($location && $date && $time) {
    try {
        $stmt = $pdo->prepare("REPLACE INTO game_info (id, location, game_date, game_time) VALUES (1, ?, ?, ?)");
        $success = $stmt->execute([$location, $date, $time]);
        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
exit;
?>