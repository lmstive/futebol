<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $player = $_POST['player'] ?? '';
    $status = $_POST['status'] ?? '';
    $month = date('Y-m');

    if (empty($player) || !in_array($status, ['OK', 'Pendente'])) {
        throw new Exception('Dados inválidos');
    }

    $payment_date = ($status === 'OK') ? date('Y-m-d H:i:s') : NULL;

    $stmt = $pdo->prepare("UPDATE payments SET status = :status, payment_date = :payment_date, month = :month WHERE player_name = :player");
    $stmt->execute([
        'status' => $status,
        'payment_date' => $payment_date,
        'month' => $month,
        'player' => $player
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>