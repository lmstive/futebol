<?php
header('Content-Type: application/json');
session_start();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === 'admin' && $password === 'destreinados123') {
    $_SESSION['admin'] = true;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
exit;
?>