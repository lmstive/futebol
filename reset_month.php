<?php
// reset_month.php (CORRIGIDO para afetar apenas o mês atual)
header('Content-Type: application/json'); 
require_once 'config.php'; // Inclui a conexão com o banco e o fuso horário

session_start(); 
$isCalledByBrowser = isset($_SERVER['HTTP_USER_AGENT']); 
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

// Se for chamado pelo navegador E não for admin, negar acesso.
if ($isCalledByBrowser && !$isAdmin) {
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado']);
    exit; 
}
// Se for chamado via Cron, $isCalledByBrowser será falso, então o script continua.

try {
    // Determina o mês atual no formato YYYY-MM
    $current_month = date('Y-m'); 

    // Prepara a query para atualizar os pagamentos APENAS DO MÊS ATUAL
    // Define status = 'Pendente' e payment_date = NULL
    // ONDE o status atual NÃO É 'Isento'
    // E ONDE o mês é o MÊS ATUAL (YYYY-MM)
    $stmt = $pdo->prepare("UPDATE payments SET status = 'Pendente', payment_date = NULL WHERE status != 'Isento' AND month = :current_month");
    
    // Executa a query, passando o mês atual como parâmetro
    $stmt->execute(['current_month' => $current_month]);
    $affectedRows = $stmt->rowCount(); // Pega o número de linhas afetadas

    // Retorna sucesso em formato JSON (para o botão) ou imprime (para o log do Cron)
    if ($isCalledByBrowser) {
        echo json_encode(['success' => true, 'message' => $affectedRows . ' jogadores resetados para Pendente no mês ' . $current_month . '.']);
    } else {
        // Mensagem para o log do Cron
        echo "Reset mensal executado para " . $current_month . ". " . $affectedRows . " jogadores resetados para Pendente.\n";
    }

} catch (PDOException $e) {
    $errorMessage = 'Erro no banco de dados ao resetar mês: ' . $e->getMessage();
    error_log($errorMessage); // Loga o erro para análise posterior (útil para Cron)
    if ($isCalledByBrowser) {
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    } else {
        echo $errorMessage . "\n";
    }
} catch (Exception $e) {
    $errorMessage = 'Erro geral ao resetar mês: ' . $e->getMessage();
    error_log($errorMessage); // Loga o erro
     if ($isCalledByBrowser) {
        echo json_encode(['success' => false, 'error' => $errorMessage]);
    } else {
        echo $errorMessage . "\n";
    }
}
?>