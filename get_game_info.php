<?php
header('Content-Type: application/json');
require_once 'config.php';

// --- Início da Lógica de Cálculo da Data ---
$today = new DateTime('now', new DateTimeZone('America/Sao_Paulo')); // Usar fuso horário
$dayOfWeek = (int)$today->format('w'); // 0 (Domingo) a 6 (Sábado)
$targetDay = 3; // Quarta-feira

$daysUntilTarget = ($targetDay - $dayOfWeek + 7) % 7;

// Se for quarta-feira e antes das 22h, o jogo é hoje
if ($dayOfWeek === $targetDay && (int)$today->format('H') < 22) {
    $daysUntilTarget = 0;
}
// Se for quarta-feira depois das 22h, ou já passou da quarta, calcula para a próxima semana
else if ($daysUntilTarget === 0 && $dayOfWeek === $targetDay && (int)$today->format('H') >= 22) {
     $daysUntilTarget = 7; // Adiciona 7 dias se for quarta após o horário
} else if ($daysUntilTarget === 0 && $dayOfWeek !== $targetDay) {
     // Caso especial: se o cálculo deu 0 mas não é quarta (não deveria acontecer com a fórmula acima, mas por segurança)
     $daysUntilTarget = 7;
}


$nextWednesday = clone $today;
$nextWednesday->modify("+$daysUntilTarget days");
$formattedDate = $nextWednesday->format('d/m/Y'); // Formato DD/MM/YYYY
// --- Fim da Lógica de Cálculo da Data ---

try {
    // Ainda busca local e hora do banco, mas usa a data calculada
    $stmt = $pdo->query("SELECT location, game_time FROM games WHERE id = 2");
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($game) {
        // Retorna a data CALCULADA e o resto do banco
        echo json_encode([
            'success' => true,
            'location' => $game['location'],
            'game_date' => $formattedDate, // <-- Usa a data calculada
            'game_time' => $game['game_time']
        ]);
    } else {
        // Se não achar o jogo no banco, retorna a data calculada e valores padrão
         echo json_encode([
            'success' => true, // Ou false, dependendo se quer indicar erro de busca
            'location' => 'Arena Bom de Bola', // Valor padrão
            'game_date' => $formattedDate, // <-- Usa a data calculada
            'game_time' => '22:00' // Valor padrão
        ]);
        // Ou retornar erro: echo json_encode(['success' => false, 'error' => 'Nenhum jogo encontrado']);
    }
} catch (PDOException $e) {
    // Em caso de erro de DB, ainda podemos retornar a data calculada
     echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'location' => 'Arena Bom de Bola', // Valor padrão
        'game_date' => $formattedDate, // <-- Usa a data calculada
        'game_time' => '22:00' // Valor padrão
    ]);
}
?>