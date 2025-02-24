<?php
header('Content-Type: application/json');
require 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM game_info ORDER BY id DESC LIMIT 1");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data ?: ['location' => 'Arena Bom de Bola', 'game_date' => '26 de Fevereiro de 2025', 'game_time' => '22:00']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>