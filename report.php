<?php
session_start();
$isAdmin = isset($_SESSION['admin']);
require_once 'config.php';

if (!$isAdmin) {
    die("Acesso negado. Faça login como admin.");
}

$month = $_GET['month'] ?? date('Y-m');
$months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];

$stmt = $pdo->prepare("SELECT player_name, type, status, payment_date FROM payments WHERE month = :month ORDER BY 
    CASE 
        WHEN type = 'Goleiro' THEN 1 
        WHEN type = 'Sim' THEN 2 
        WHEN type = 'Não' THEN 3 
    END, player_name ASC");
$stmt->execute(['month' => $month]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

$paid = count(array_filter($players, fn($p) => $p['status'] === 'OK'));
$pending = count($players) - $paid;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Mensal - Destreinados FC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .player-paid { color: green; font-weight: bold; }
        .player-pending { color: red; }
        h1 { color: #27ae60; }
        .summary { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1>Relatório Mensal - <?php echo $months[(int)substr($month, 5) - 1] . ' ' . substr($month, 0, 4); ?></h1>
        <form method="GET" class="mb-3">
            <label for="month" class="form-label">Selecione o Mês:</label>
            <select name="month" id="month" class="form-select d-inline w-auto" onchange="this.form.submit()">
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthValue = sprintf('2025-%02d', $m);
                    $selected = $monthValue === $month ? 'selected' : '';
                    echo "<option value='$monthValue' $selected>" . $months[$m-1] . " 2025</option>";
                }
                ?>
            </select>
        </form>
        <p class="summary">Total de Pagantes: <?php echo $paid; ?> | Total de Pendentes: <?php echo $pending; ?></p>
        <a href="index.php" class="btn btn-secondary mb-3">Voltar</a>
        <a href="export_report.php?month=<?php echo $month; ?>" class="btn btn-success mb-3">Exportar como PDF</a>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Jogador</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Data do Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?php echo $player['player_name']; ?></td>
                        <td><?php echo $player['type']; ?></td>
                        <td class="<?php echo $player['status'] === 'OK' ? 'player-paid' : 'player-pending'; ?>">
                            <?php echo $player['status']; ?>
                        </td>
                        <td><?php echo $player['payment_date'] ? date('d/m/Y H:i', strtotime($player['payment_date'])) : 'N/A'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>