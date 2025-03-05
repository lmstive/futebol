<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// Verificar se é admin
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Faça login como admin.']);
    exit;
}

try {
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
        throw new Exception('Nenhuma foto foi enviada.');
    }

    $file = $_FILES['photo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Verificar tipo e tamanho do arquivo
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Formato inválido. Use JPG, PNG ou GIF.');
    }
    if ($file['size'] > $maxSize) {
        throw new Exception('Arquivo muito grande. Máximo 5MB.');
    }

    // Diretório de upload
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Nome único para o arquivo
    $filename = uniqid() . '-' . basename($file['name']);
    $destination = $uploadDir . $filename;

    // Mover o arquivo para o diretório
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Erro ao salvar a foto.');
    }

    // Salvar no banco de dados
    $stmt = $pdo->prepare("INSERT INTO photos (filename) VALUES (:filename)");
    $stmt->execute(['filename' => $filename]);

    echo json_encode(['success' => true, 'message' => 'Foto enviada com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>