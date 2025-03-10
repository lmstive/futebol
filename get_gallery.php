<?php
// Arquivo: upload_photo.php
session_start();
require_once 'config.php';

// Verificar se o usuário é administrador
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit;
}

// Verificar se o diretório de uploads existe, se não, criar
$uploadDir = 'uploads/gallery/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'Falha ao criar diretório de uploads']);
        exit;
    }
}

// Verificar se há arquivo enviado
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Erro no upload do arquivo']);
    exit;
}

// Obter dados do arquivo e do formulário
$file = $_FILES['photo'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

$caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';
$date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');

// Validar tipo de arquivo (apenas imagens)
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de arquivo não permitido. Apenas JPG, JPEG, PNG e GIF são aceitos.']);
    exit;
}

// Validar tamanho do arquivo (max 5MB)
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($fileSize > $maxFileSize) {
    echo json_encode(['success' => false, 'error' => 'Arquivo muito grande. Tamanho máximo: 5MB']);
    exit;
}

// Gerar nome único para o arquivo
$newFileName = uniqid('img_') . '.' . $fileExt;
$destination = $uploadDir . $newFileName;

// Mover o arquivo
if (!move_uploaded_file($fileTmpName, $destination)) {
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar arquivo']);
    exit;
}

// Salvar informações no banco de dados
try {
    $stmt = $pdo->prepare("INSERT INTO gallery (filename, caption, date, uploader) VALUES (?, ?, ?, ?)");
    $stmt->execute([$newFileName, $caption, $date, 'admin']);
    
    echo json_encode(['success' => true, 'message' => 'Foto enviada com sucesso']);
} catch (PDOException $e) {
    // Em caso de erro, excluir o arquivo
    unlink($destination);
    echo json_encode(['success' => false, 'error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>

<?php
// Arquivo: get_gallery.php
require_once 'config.php';

try {
    // Obter todas as fotos do banco de dados
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY date DESC, id DESC");
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'photos' => $photos]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao carregar galeria: ' . $e->getMessage()]);
}
?>

<?php
// Arquivo: delete_photo.php
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

$photoId = $_POST['photo_id'];

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
?>

<?php
// Arquivo: edit_photo.php (para atualizar a legenda)
session_start();
require_once 'config.php';

// Verificar se o usuário é administrador
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit;
}

// Verificar se ID e nova legenda foram enviados
if (!isset($_POST['photo_id']) || empty($_POST['photo_id']) || !isset($_POST['caption'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

$photoId = $_POST['photo_id'];
$caption = trim($_POST['caption']);

try {
    // Atualizar a legenda no banco de dados
    $stmt = $pdo->prepare("UPDATE gallery SET caption = ? WHERE id = ?");
    $stmt->execute([$caption, $photoId]);
    
    echo json_encode(['success' => true, 'message' => 'Legenda atualizada com sucesso']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao atualizar legenda: ' . $e->getMessage()]);
}
?>