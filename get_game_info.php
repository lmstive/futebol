<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT location, game_date, game_time FROM games WHERE id = 1");
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($game) {
        echo json_encode(['success' => true, 'location' => $game['location'], 'game_date' => $game['game_date'], 'game_time' => $game['game_time']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nenhum jogo encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>