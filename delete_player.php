<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $player_name = $_POST['player_name'] ?? '';

    if (empty($player_name)) {
        throw new Exception('Nome do jogador inválido');
    }

    $stmt = $pdo->prepare("DELETE FROM payments WHERE player_name = :player_name");
    $stmt->execute(['player_name' => $player_name]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
