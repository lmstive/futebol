<?php
header('Content-Type: application/json');
require 'config.php';

try {
    $today = new DateTime();
    $firstDayOfMonth = new DateTime('first day of this month');
    $stmt = $pdo->query("SELECT MIN(last_reset) as oldest_reset FROM payments");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastReset = $row['oldest_reset'] ? new DateTime($row['oldest_reset']) : null;

    if (!$lastReset || $lastReset < $firstDayOfMonth) {
        $stmt = $pdo->prepare("UPDATE payments SET status = 'Pendente', last_reset = CURDATE()");
        $stmt->execute();
    }

    $stmt = $pdo->query("SELECT player, status FROM payments");
    $payments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $payments[$row['player']] = $row['status'];
    }
    echo json_encode($payments);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>