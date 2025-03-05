<?php
require_once 'config.php';

try {
    $today = new DateTime();
    $dayOfWeek = (int)$today->format('w'); // 0 = Domingo, 6 = Sábado
    $hours = (int)$today->format('H');

    // Só atualiza se for sábado após 15h
    if ($dayOfWeek === 6 && $hours >= 15) {
        $nextSaturday = $today->modify('+7 days'); // Próximo sábado
        $months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $date = $nextSaturday->format('j') . ' de ' . $months[(int)$nextSaturday->format('n') - 1] . ' de ' . $nextSaturday->format('Y');

        $stmt = $pdo->prepare("REPLACE INTO games (id, location, game_date, game_time) VALUES (1, :location, :game_date, :game_time)");
        $stmt->execute([
            'location' => 'Arena Biasi',
            'game_date' => $date,
            'game_time' => '15:00'
        ]);
    }
} catch (Exception $e) {
    file_put_contents('update_game_error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
}
?>