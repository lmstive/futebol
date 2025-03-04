<?php
session_start();
require_once 'config.php';

// Verificar se o admin está logado
if (!isset($_SESSION['admin'])) {
    die("Acesso negado. Faça login como admin.");
}

// Verificar se o FPDF está disponível
if (!file_exists('fpdf/fpdf.php')) {
    die("Erro: Biblioteca FPDF não encontrada. Certifique-se de que a pasta 'fpdf' está no diretório correto.");
}

require('fpdf/fpdf.php');

// Configurar o mês
$month = $_GET['month'] ?? date('Y-m');
$months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$month_name = $months[(int)substr($month, 5) - 1] . ' ' . substr($month, 0, 4);

try {
    // Buscar os dados
    $stmt = $pdo->prepare("SELECT player_name, type, status, payment_date FROM payments WHERE month = :month ORDER BY 
        CASE 
            WHEN type = 'Goleiro' THEN 1 
            WHEN type = 'Sim' THEN 2 
            WHEN type = 'Não' THEN 3 
        END, player_name ASC");
    $stmt->execute(['month' => $month]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Criar o PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode("Relatório Mensal - $month_name"), 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(60, 10, 'Jogador', 1); // Aumentei a largura para caber nomes longos
    $pdf->Cell(30, 10, 'Tipo', 1);
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Cell(60, 10, 'Data do Pagamento', 1); // Aumentei a largura
    $pdf->Ln();

    foreach ($players as $player) {
        $pdf->Cell(60, 10, utf8_decode($player['player_name']), 1);
        $pdf->Cell(30, 10, utf8_decode($player['type']), 1);
        $pdf->Cell(30, 10, utf8_decode($player['status']), 1);
        $pdf->Cell(60, 10, $player['payment_date'] ? date('d/m/Y H:i', strtotime($player['payment_date'])) : 'N/A', 1);
        $pdf->Ln();
    }

    // Saída do PDF
    $pdf->Output('D', "relatorio_$month.pdf");
} catch (Exception $e) {
    die("Erro ao gerar o PDF: " . $e->getMessage());
}
?>