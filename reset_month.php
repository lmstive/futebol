<?php
session_start();
require_once 'config.php'; // Garante que $pdo está disponível
header('Content-Type: application/json'); // Resposta para o AJAX

// 1. Verificar se é Admin
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Faça login como admin.']);
    exit; // Interrompe a execução se não for admin
}

// 2. Obter o mês atual no formato YYYY-MM
$currentMonth = date('Y-m');

// 3. Iniciar uma transação (garante que todas as atualizações ocorram ou nenhuma)
$pdo->beginTransaction();

try {
    // 4. ATUALIZAR A COLUNA 'month' PARA O MÊS ATUAL EM TODOS OS REGISTROS
    //    Também é uma boa ideia resetar a data de pagamento (payment_date) para NULL
    $stmtUpdateMonth = $pdo->prepare("UPDATE payments SET month = :current_month, payment_date = NULL");
    $stmtUpdateMonth->execute(['current_month' => $currentMonth]);

    // 5. RESETAR O STATUS para 'Pendente', EXCETO para os 'Isentos'
    //    Atualiza apenas os que não são 'Isento'
    $stmtUpdateStatus = $pdo->prepare("UPDATE payments SET status = 'Pendente' WHERE status != 'Isento'");
    $stmtUpdateStatus->execute();

    // 6. Se tudo correu bem, confirma as alterações no banco
    $pdo->commit();

    // 7. Enviar resposta de sucesso para o JavaScript
    echo json_encode(['success' => true, 'message' => 'Mês resetado com sucesso para ' . $currentMonth]);

} catch (PDOException $e) {
    // 8. Se ocorreu algum erro, desfaz todas as alterações da transação
    $pdo->rollBack();

    // 9. Enviar resposta de erro para o JavaScript
    error_log("Erro ao resetar mês: " . $e->getMessage()); // Log do erro no servidor
    echo json_encode(['success' => false, 'error' => 'Erro ao conectar ou atualizar o banco de dados. Verifique os logs do servidor.']);
}
?>