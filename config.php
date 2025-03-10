<?php
// Configurar fuso horário para Brasil
date_default_timezone_set('America/Sao_Paulo');

$host = 'localhost';
$dbname = 'luismi71_destreinados_bd';
$username = 'luismi71_admin';
$password = 'destreinados';

try {
    // Conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar timezone do MySQL também
    $pdo->exec("SET time_zone = '-03:00'");
    
    // Opcional: Configurar para retornar datas no formato brasileiro em todas as consultas
    // $pdo->exec("SET lc_time_names = 'pt_BR'");
    
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}