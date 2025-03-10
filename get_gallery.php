<?php
// Configurar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir o fuso horário para Brasil
date_default_timezone_set('America/Sao_Paulo');

require_once 'config.php';

try {
    // Verificar se a tabela gallery existe
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'gallery'");
    if ($tableCheck->rowCount() == 0) {
        // Se a tabela não existe, retornar uma lista vazia
        echo json_encode(['success' => true, 'photos' => []]);
        exit;
    }
    
    // Obter todas as fotos do banco de dados
    // Garantir que a data esteja no formato correto para exibição
    $stmt = $pdo->query("SELECT id, filename, caption, 
                          DATE_FORMAT(date, '%d/%m/%Y') as formatted_date, 
                          date,
                          created_at 
                         FROM gallery 
                         ORDER BY date DESC, id DESC");
                         
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'photos' => $photos]);
} catch (PDOException $e) {
    // Registrar erro em log
    error_log("Erro ao carregar galeria: " . $e->getMessage());
    
    echo json_encode(['success' => false, 'error' => 'Erro ao carregar galeria: ' . $e->getMessage()]);
}