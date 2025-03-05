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
    $photoId = $_POST['photo_id'] ?? '';
    if (empty($photoId)) {
        throw new Exception('ID da foto não fornecido.');
    }

    // Buscar o nome do arquivo no banco
    $stmt = $pdo->prepare("SELECT filename FROM photos WHERE id = :id");
    $stmt->execute(['id' => $photoId]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$photo) {
        throw new Exception('Foto não encontrada.');
    }

    // Deletar o arquivo do servidor
    $filePath = 'uploads/' . $photo['filename'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Deletar do banco de dados
    $stmt = $pdo->prepare("DELETE FROM photos WHERE id = :id");
    $stmt->execute(['id' => $photoId]);

    echo json_encode(['success' => true, 'message' => 'Foto excluída com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>