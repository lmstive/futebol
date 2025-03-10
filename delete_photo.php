<?php
// Configurar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Verificar se o usuário é administrador
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit;
}

// Verificar se ID foi enviado
if (!isset($_POST['photo_id']) || empty($_POST['photo_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID da foto não fornecido']);
    exit;
}

$photoId = intval($_POST['photo_id']);

try {
    // Primeiro, obtém o nome do arquivo
    $stmt = $pdo->prepare("SELECT filename FROM gallery WHERE id = ?");
    $stmt->execute([$photoId]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$photo) {
        echo json_encode(['success' => false, 'error' => 'Foto não encontrada']);
        exit;
    }
    
    // Excluir o arquivo físico
    $filePath = 'uploads/gallery/' . $photo['filename'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Excluir registro do banco de dados
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->execute([$photoId]);
    
    echo json_encode(['success' => true, 'message' => 'Foto excluída com sucesso']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao excluir foto: ' . $e->getMessage()]);
}