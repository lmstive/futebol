<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $old_name = $_POST['old_name'] ?? '';
    $player_name = $_POST['player_name'] ?? '';
    $type = $_POST['type'] ?? '';

    if (empty($old_name) || empty($player_name) || !in_array($type, ['Goleiro', 'Sim', 'Não'])) {
        throw new Exception('Dados inválidos');
    }

    $stmt = $pdo->prepare("UPDATE payments SET player_name = :player_name, type = :type WHERE player_name = :old_name");
    $stmt->execute([
        'player_name' => $player_name,
        'type' => $type,
        'old_name' => $old_name
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>