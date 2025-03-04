<?php
require_once 'config.php';
require('fpdf/fpdf.php');

$month = $_GET['month'] ?? date('Y-m');
$months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
$month_name = $months[(int)substr($month, 5) - 1] . ' ' . substr($month, 0, 4);

$stmt = $pdo->prepare("SELECT player_name, type, status, payment_date FROM payments WHERE month = :month ORDER BY 
    CASE 
        WHEN type = 'Goleiro' THEN 1 
        WHEN type = 'Sim' THEN 2 
        WHEN type = 'Não' THEN 3 
    END, player_name ASC");
$stmt->execute(['month' => $month]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode("Relatório Mensal - $month_name"), 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Jogador', 1);
$pdf->Cell(30, 10, 'Tipo', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Cell(50, 10, 'Data do Pagamento', 1);
$pdf->Ln();

foreach ($players as $player) {
    $pdf->Cell(50, 10, utf8_decode($player['player_name']), 1);
    $pdf->Cell(30, 10, utf8_decode($player['type']), 1);
    $pdf->Cell(30, 10, $player['status'], 1);
    $pdf->Cell(50, 10, $player['payment_date'] ? date('d/m/Y H:i', strtotime($player['payment_date'])) : 'N/A', 1);
    $pdf->Ln();
}

$pdf->Output('D', "relatorio_$month.pdf");
?>