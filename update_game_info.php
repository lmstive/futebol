<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $location = $_POST['location'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';

    if (empty($location) || empty($date) || empty($time)) {
        throw new Exception('Dados inválidos');
    }

    $stmt = $pdo->prepare("INSERT INTO games (location, game_date, game_time) VALUES (:location, :date, :time) 
        ON DUPLICATE KEY UPDATE location = :location, game_date = :date, game_time = :time");
    $stmt->execute(['location' => $location, 'date' => $date, 'time' => $time]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
