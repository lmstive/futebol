<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $location = $_POST['location'] ?? 'Arena Biasi';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '15:00';

    if (empty($location) || empty($date) || empty($time)) {
        throw new Exception('Dados inválidos');
    }

    // Sempre atualiza o jogo com id = 1
    $stmt = $pdo->prepare("REPLACE INTO games (id, location, game_date, game_time) VALUES (1, :location, :game_date, :game_time)");
    $stmt->execute([
        'location' => $location,
        'game_date' => $date,
        'game_time' => $time
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>