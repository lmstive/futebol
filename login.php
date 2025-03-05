<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
// Substitua pelo hash gerado para "destreinados123"
$stored_hash = '$2y$10$qPK07Vm8FRVN7J3//f6MC.fSkY3MWZbitoge4H5L0jJ8Ue0so0D1S'; // Gere o seu e substitua aqui

if ($username === 'admin' && password_verify($password, $stored_hash)) {
    $_SESSION['admin'] = true;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>