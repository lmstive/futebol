<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $player_name = $_POST['player_name'] ?? '';
    $type = $_POST['type'] ?? '';
    $month = date('Y-m');
    $status = ($type === 'Goleiro') ? 'Isento' : 'Pendente'; // Goleiros começam como "Isento"

    if (empty($player_name) || !in_array($type, ['Goleiro', 'Sim', 'Não'])) {
        throw new Exception('Dados inválidos');
    }

    $stmt = $pdo->prepare("INSERT INTO payments (player_name, type, status, month) VALUES (:player_name, :type, :status, :month)");
    $stmt->execute([
        'player_name' => $player_name,
        'type' => $type,
        'status' => $status,
        'month' => $month
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>