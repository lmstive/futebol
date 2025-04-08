<?php
// update_game_info.php (Versão com UPDATE direto no ID 2)
header('Content-Type: application/json');
require_once 'config.php';

// Iniciar sessão para verificar admin (boa prática)
session_start();
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit;
}

try {
    $location = $_POST['location'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $game_id = 2; // Definir o ID do jogo a ser atualizado

    if (empty($location) || empty($date) || empty($time)) {
        throw new Exception('Dados inválidos recebidos');
    }

    // Tentar converter a data recebida para o formato YYYY-MM-DD se necessário
    // O JavaScript parece enviar "DD de MMMM de YYYY", precisamos converter
    try {
        // Remover " de " e tentar interpretar
        $date_parts = explode(' de ', $date);
        if (count($date_parts) === 3) {
            $day = $date_parts[0];
            $month_name = $date_parts[1];
            $year = $date_parts[2];
            
            $months_pt = ['janeiro' => '01', 'fevereiro' => '02', 'março' => '03', 'abril' => '04', 'maio' => '05', 'junho' => '06', 'julho' => '07', 'agosto' => '08', 'setembro' => '09', 'outubro' => '10', 'novembro' => '11', 'dezembro' => '12'];
            $month_num = $months_pt[strtolower($month_name)] ?? null;

            if ($month_num) {
                $date_sql = sprintf('%s-%s-%s', $year, $month_num, str_pad($day, 2, '0', STR_PAD_LEFT));
                // Validar se a data convertida é válida
                $d = DateTime::createFromFormat('Y-m-d', $date_sql);
                if (!$d || $d->format('Y-m-d') !== $date_sql) {
                     throw new Exception('Formato de data inválido após conversão: ' . $date);
                }
                 $date = $date_sql; // Usa a data convertida para o SQL
            } else {
                 throw new Exception('Nome do mês inválido recebido: ' . $month_name);
            }
        } else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
             // Se não veio no formato "DD de MMMM de YYYY" e nem como YYYY-MM-DD
             throw new Exception('Formato de data inesperado recebido: ' . $date);
        }
        // Se já veio como YYYY-MM-DD, usa direto.

    } catch (Exception $e) {
         throw new Exception('Erro ao processar data: ' . $e->getMessage());
    }


    // Usar UPDATE direto no ID específico
    $stmt = $pdo->prepare("UPDATE games SET location = :location, game_date = :date, game_time = :time WHERE id = :id");
    $success = $stmt->execute([
        'location' => $location, 
        'date' => $date, // Data já deve estar no formato YYYY-MM-DD
        'time' => $time,
        'id' => $game_id 
    ]);

    if ($success) {
         echo json_encode(['success' => true]);
    } else {
         // Isso geralmente não acontece sem lançar uma PDOException, mas por segurança
         throw new Exception('Falha ao executar a atualização no banco de dados.');
    }

} catch (PDOException $e) {
    // Erro específico do banco de dados
    echo json_encode(['success' => false, 'error' => 'Erro de Banco de Dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Outros erros (dados inválidos, erro na data, etc.)
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>