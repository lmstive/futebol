<?php
// Configurar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir o fuso horário para Brasil
date_default_timezone_set('America/Sao_Paulo');

// Criar arquivo de log para debug
file_put_contents('upload_log.txt', "Iniciando upload: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

session_start();
require_once 'config.php';

// Verificar se o usuário é administrador
if (!isset($_SESSION['admin'])) {
    file_put_contents('upload_log.txt', "Erro: Acesso não autorizado\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit;
}

// Log dos dados recebidos
file_put_contents('upload_log.txt', "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents('upload_log.txt', "FILES: " . print_r($_FILES, true) . "\n", FILE_APPEND);

// Verificar se o diretório de uploads existe, se não, criar
$uploadDir = 'uploads/gallery/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        file_put_contents('upload_log.txt', "Erro: Falha ao criar diretório de uploads\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Falha ao criar diretório de uploads']);
        exit;
    }
}

// Verificar permissões do diretório
if (!is_writable($uploadDir)) {
    file_put_contents('upload_log.txt', "Erro: Diretório não tem permissão de escrita: $uploadDir\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Diretório de uploads sem permissão de escrita']);
    exit;
}

// Verificar se há arquivo enviado
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $errorMessage = "Erro no upload: ";
    if (isset($_FILES['photo'])) {
        switch ($_FILES['photo']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $errorMessage .= "O arquivo excede o tamanho máximo permitido pelo PHP.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage .= "O arquivo excede o tamanho máximo permitido pelo formulário.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage .= "O arquivo foi parcialmente enviado.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage .= "Nenhum arquivo foi enviado.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errorMessage .= "Pasta temporária não encontrada.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errorMessage .= "Falha ao escrever arquivo no disco.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $errorMessage .= "Upload interrompido por uma extensão PHP.";
                break;
            default:
                $errorMessage .= "Erro desconhecido.";
        }
    } else {
        $errorMessage .= "Nenhum arquivo enviado.";
    }
    
    file_put_contents('upload_log.txt', $errorMessage . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => $errorMessage]);
    exit;
}

// Obter dados do arquivo e do formulário
$file = $_FILES['photo'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

$caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';

// Tratamento especial para a data
$date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
file_put_contents('upload_log.txt', "Data original recebida: $date\n", FILE_APPEND);

// Garantir que a data seja armazenada corretamente no formato Y-m-d
if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
    // Converter do formato DD/MM/YYYY para YYYY-MM-DD
    $dateParts = explode('/', $date);
    $date = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
    file_put_contents('upload_log.txt', "Data convertida de DD/MM/YYYY: $date\n", FILE_APPEND);
}

// Verificar se a data é válida, caso contrário usar hoje
if (!strtotime($date)) {
    $date = date('Y-m-d');
    file_put_contents('upload_log.txt', "Data inválida, usando hoje: $date\n", FILE_APPEND);
}

// Validar tipo de arquivo (apenas imagens)
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($fileExt, $allowedExtensions)) {
    file_put_contents('upload_log.txt', "Erro: Tipo de arquivo não permitido: $fileExt\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Tipo de arquivo não permitido. Apenas JPG, JPEG, PNG e GIF são aceitos.']);
    exit;
}

// Validar tamanho do arquivo (max 5MB)
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($fileSize > $maxFileSize) {
    file_put_contents('upload_log.txt', "Erro: Arquivo muito grande: $fileSize bytes\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Arquivo muito grande. Tamanho máximo: 5MB']);
    exit;
}

// Gerar nome único para o arquivo
$newFileName = uniqid('img_') . '.' . $fileExt;
$destination = $uploadDir . $newFileName;

// Mover o arquivo
if (!move_uploaded_file($fileTmpName, $destination)) {
    file_put_contents('upload_log.txt', "Erro: Falha ao mover arquivo para: $destination\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Falha ao salvar arquivo']);
    exit;
}

file_put_contents('upload_log.txt', "Arquivo movido com sucesso para: $destination\n", FILE_APPEND);
file_put_contents('upload_log.txt', "Data final para inserção no banco: $date\n", FILE_APPEND);

// Verificar se a tabela gallery existe
try {
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'gallery'");
    if ($tableCheck->rowCount() == 0) {
        // Criar a tabela gallery se não existir
        file_put_contents('upload_log.txt', "Criando tabela gallery...\n", FILE_APPEND);
        $pdo->exec("CREATE TABLE IF NOT EXISTS `gallery` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `filename` varchar(255) NOT NULL,
            `caption` text DEFAULT NULL,
            `date` date NOT NULL,
            `uploader` varchar(50) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
} catch (PDOException $e) {
    file_put_contents('upload_log.txt', "Erro ao verificar/criar tabela: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'Erro ao verificar/criar tabela: ' . $e->getMessage()]);
    // Não saímos aqui para tentar a inserção de qualquer forma
}

// Salvar informações no banco de dados
try {
    file_put_contents('upload_log.txt', "Tentando inserir no banco de dados...\n", FILE_APPEND);
    $stmt = $pdo->prepare("INSERT INTO gallery (filename, caption, date, uploader) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$newFileName, $caption, $date, 'admin']);
    
    if ($result) {
        file_put_contents('upload_log.txt', "Sucesso! Foto inserida no banco de dados.\n", FILE_APPEND);
        echo json_encode(['success' => true, 'message' => 'Foto enviada com sucesso']);
    } else {
        file_put_contents('upload_log.txt', "Erro na execução do SQL: " . print_r($stmt->errorInfo(), true) . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Erro ao inserir no banco de dados']);
    }
} catch (PDOException $e) {
    file_put_contents('upload_log.txt', "Erro PDO: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Em caso de erro, excluir o arquivo
    unlink($destination);
    file_put_contents('upload_log.txt', "Arquivo excluído após erro.\n", FILE_APPEND);
    
    echo json_encode(['success' => false, 'error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}