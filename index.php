<?php
session_start();
$isAdmin = isset($_SESSION['admin']);
require_once 'config.php';

// Carregar os jogadores do banco com ordenação alfabética dentro de cada tipo
$stmt = $pdo->query("SELECT player_name, type, status FROM payments ORDER BY 
    CASE 
        WHEN type = 'Goleiro' THEN 1 
        WHEN type = 'Sim' THEN 2 
        WHEN type = 'Não' THEN 3 
    END, player_name ASC");
$sortedPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar se há um tema definido no cookie
$darkMode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true';
?>

<!DOCTYPE html>
<html lang="pt-BR" class="<?php echo $darkMode ? 'dark-mode' : ''; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destreinados Futebol Clube</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lg-zoom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lg-thumbnail.css">
    <style>
        :root {
            --primary-color: #27ae60;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #1a252f;
            --text-color: #333;
            --bg-color: #f5f5f5;
            --card-bg: #fff;
            --header-bg: var(--secondary-color);
            --footer-bg: var(--secondary-color);
        }

        .dark-mode {
            --text-color: #f0f0f0;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --header-bg: #0f172a;
            --footer-bg: #0f172a;
            --primary-color: #4ade80;
            --secondary-color: #334155;
            --accent-color: #60a5fa;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        .player-paid {
            color: var(--primary-color);
            font-weight: bold;
        }

        .player-pending {
            color: #e74c3c;
        }

        .player-exempt {
            color: var(--accent-color);
            font-weight: bold;
        }

        .login-form {
            max-width: 300px;
        }

        header {
            background-color: var(--header-bg);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #219653;
            border-color: #219653;
        }

        footer {
            background-color: var(--footer-bg);
            margin-top: 3rem;
            transition: background-color 0.3s;
        }

        h2 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .logo {
            max-height: 60px;
            margin-right: 15px;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s;
            margin-bottom: 20px;
            background-color: var(--card-bg);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--secondary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .list-group-item {
            border-left: 4px solid transparent;
            transition: all 0.2s ease, background-color 0.3s;
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .list-group-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            border-left-color: var(--primary-color);
        }

        .dark-mode .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            color: var(--text-color);
        }

        .table thead th {
            background-color: var(--secondary-color);
            color: white;
            border-bottom: none;
        }

        .table tbody td {
            background-color: var(--card-bg);
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .dark-mode .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 20px;
        }

        .badge-primary {
            background-color: var(--accent-color);
        }

        .badge-success {
            background-color: var(--primary-color);
        }

        .badge-danger {
            background-color: #e74c3c;
        }

        .badge-info {
            background-color: var(--secondary-color);
        }

        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .modal-header {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-close {
            filter: invert(1) brightness(200%);
        }

        .toast {
            background-color: var(--secondary-color);
            color: white;
        }

        .toast-header {
            background-color: var(--primary-color);
            color: white;
        }

        .player-list-container {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .game-info-card {
            border-left: 5px solid var(--primary-color);
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background-color: var(--card-bg);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .stats-label {
            font-size: 1rem;
            color: var(--text-color);
            text-transform: uppercase;
        }

        .stats-icon {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-ok {
            background-color: var(--primary-color);
            color: white;
        }

        .status-pendente {
            background-color: #e74c3c;
            color: white;
        }

        .status-isento {
            background-color: var(--accent-color);
            color: white;
        }

        .dark-mode-toggle {
            cursor: pointer;
            font-size: 1.5rem;
            margin-right: 15px;
            color: white;
            transition: color 0.3s;
        }

        .dark-mode-toggle:hover {
            color: var(--primary-color);
        }

        .form-control,
        .form-select {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: rgba(0, 0, 0, 0.1);
        }

        .dark-mode .form-control,
        .dark-mode .form-select {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .input-group-text {
            background-color: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .nav-tabs .nav-link {
            color: var(--text-color);
        }

        .nav-tabs .nav-link.active {
            background-color: var(--card-bg);
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        /* Galeria de Fotos */
        .gallery-container {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .gallery-item {
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: scale(1.03);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .gallery-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 12px;
            font-size: 0.9rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .gallery-item:hover .gallery-caption {
            opacity: 1;
        }

        .gallery-upload-form {
            background-color: var(--card-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Estilos para o botão de excluir */
        .delete-photo {
            opacity: 0.7;
            transition: opacity 0.3s;
            z-index: 10;
        }

        .delete-photo:hover {
            opacity: 1;
        }

        /* Adicionar efeito de hover vermelho para botão de exclusão */
        .gallery-item .btn-danger {
            background-color: rgba(220, 53, 69, 0.8);
            border-color: transparent;
        }

        .gallery-item .btn-danger:hover {
            background-color: rgba(220, 53, 69, 1);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .login-form input {
                margin-bottom: 5px;
            }

            .stats-card {
                margin-bottom: 15px;
            }

            .gallery-item img {
                height: 150px;
            }

            /* Para dispositivos móveis, sempre mostrar os botões */
            .delete-photo {
                opacity: 1;
            }

            .gallery-caption {
                opacity: 1;
                font-size: 0.8rem;
                padding: 5px 8px;
            }
        }
    </style>
</head>

<body>
    <header class="text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="logo.png" alt="Logo Destreinados FC" class="logo">
                <h1 class="h3 mb-0">Destreinados Futebol Clube</h1>
            </div>
            <div class="d-flex align-items-center">
                <div id="darkModeToggle" class="dark-mode-toggle me-3">
                    <?php if ($darkMode): ?>
                        <i class="fas fa-sun"></i>
                    <?php else: ?>
                        <i class="fas fa-moon"></i>
                    <?php endif; ?>
                </div>
                <?php if (!$isAdmin): ?>
                    <form id="loginForm" class="login-form d-flex flex-column flex-md-row gap-2">
                        <input type="text" class="form-control form-control-sm" id="username" placeholder="Usuário" required>
                        <input type="password" class="form-control form-control-sm" id="password" placeholder="Senha" required>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i> Entrar
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-white">
                        <span class="me-2"><i class="fas fa-user-shield me-1"></i> Admin Logado</span>
                        <button class="btn btn-sm btn-danger" id="logout">
                            <i class="fas fa-sign-out-alt me-1"></i> Sair
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <!-- Navegação por abas -->
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
                    <i class="fas fa-home me-1"></i> Início
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery-tab-pane" type="button" role="tab" aria-controls="gallery-tab-pane" aria-selected="false">
                    <i class="fas fa-images me-1"></i> Galeria
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Aba de Início -->
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="row">
                    <!-- Jogo -->
                    <div class="col-md-8">
                        <section class="game-info-card" id="gameInfo">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="m-0 border-0">Próximo Jogo</h2>
                                <?php if ($isAdmin): ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateGameModal">
                                        <i class="fas fa-edit me-1"></i> Atualizar
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><i class="fas fa-map-marker-alt me-2 text-primary"></i> <strong>Local:</strong> <span id="gameLocation">Carregando...</span></p>
                                    <p><i class="fas fa-calendar-alt me-2 text-primary"></i> <strong>Data:</strong> <span id="gameDate">Carregando...</span></p>
                                    <p><i class="fas fa-clock me-2 text-primary"></i> <strong>Horário:</strong> <span id="gameTime">Carregando...</span></p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Estatísticas -->
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-icon"><i class="fas fa-users"></i></div>
                                    <div class="stats-value" id="total-players">-</div>
                                    <div class="stats-label">Jogadores</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="stats-value" id="payment-percentage">-</div>
                                    <div class="stats-label">Pagos</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-icon"><i class="fas fa-user-shield"></i></div>
                                    <div class="stats-value" id="total-goalkeepers">-</div>
                                    <div class="stats-label">Goleiros</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stats-card">
                                    <div class="stats-icon"><i class="fas fa-calendar-check"></i></div>
                                    <div class="stats-value" id="total-monthly">-</div>
                                    <div class="stats-label">Mensalistas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="player-list-container">
                    <h2>Jogadores</h2>
                    <div class="row" id="playersList">
                        <!-- Lista gerada dinamicamente pelo JavaScript -->
                    </div>
                </section>

                <section class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="m-0 border-0">Lista de Pagamentos - <?php
                                                                        $months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                                                                        echo $months[date('n') - 1] . ' ' . date('Y');
                                                                        ?></h2>
                        <?php if ($isAdmin): ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                                <i class="fas fa-plus me-1"></i> Adicionar Jogador
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-user me-1"></i> Jogador</th>
                                        <th><i class="fas fa-calendar-check me-1"></i> Mensalista</th>
                                        <th><i class="fas fa-money-bill-wave me-1"></i> Status</th>
                                        <th><i class="fas fa-cogs me-1"></i> Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentTable">
                                    <?php foreach ($sortedPlayers as $player): ?>
                                        <tr>
                                            <td><?php echo $player['player_name']; ?></td>
                                            <td>
                                                <?php if ($player['type'] === 'Goleiro'): ?>
                                                    <span class="badge bg-primary"><i class="fas fa-hands me-1"></i> Goleiro</span>
                                                <?php elseif ($player['type'] === 'Sim'): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Sim</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i> Não</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($player['status'] === 'OK'): ?>
                                                    <span class="status-badge status-ok"><i class="fas fa-check-circle me-1"></i> OK</span>
                                                <?php elseif ($player['status'] === 'Pendente'): ?>
                                                    <span class="status-badge status-pendente"><i class="fas fa-exclamation-circle me-1"></i> Pendente</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-isento"><i class="fas fa-shield-alt me-1"></i> Isento</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($isAdmin): ?>
                                                    <button class="btn btn-sm btn-warning toggle-payment" data-player="<?php echo $player['player_name']; ?>" data-type="<?php echo $player['type']; ?>">
                                                        <i class="fas fa-exchange-alt me-1"></i> Alterar
                                                    </button>
                                                    <button class="btn btn-sm btn-primary edit-player" data-player="<?php echo $player['player_name']; ?>" data-type="<?php echo $player['type']; ?>" data-bs-toggle="modal" data-bs-target="#editPlayerModal">
                                                        <i class="fas fa-edit me-1"></i> Editar
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-player" data-player="<?php echo $player['player_name']; ?>">
                                                        <i class="fas fa-trash-alt me-1"></i> Excluir
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($isAdmin): ?>
                            <div class="mt-3">
                                <a href="report.php?month=<?php echo date('Y-m'); ?>" target="_blank" class="btn btn-info">
                                    <i class="fas fa-file-alt me-1"></i> Relatório Mensal
                                </a>
                                <button class="btn btn-primary" id="exportCSV">
                                    <i class="fas fa-file-csv me-1"></i> Exportar CSV
                                </button>
                                <button class="btn btn-secondary" id="resetMonthBtn">
                                    <i class="fas fa-calendar-alt me-1"></i> Reiniciar Mês
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Aba da Galeria -->
            <div class="tab-pane fade" id="gallery-tab-pane" role="tabpanel" aria-labelledby="gallery-tab" tabindex="0">
                <section class="gallery-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="m-0 border-0">Galeria de Fotos</h2>
                        <?php if ($isAdmin): ?>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                                <i class="fas fa-upload me-1"></i> Upload de Fotos
                            </button>
                        <?php endif; ?>
                    </div>

                    <div id="galleryContent" class="row">
                        <!-- Imagens serão carregadas aqui dinamicamente -->
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando fotos...</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Jogador -->
    <?php if ($isAdmin): ?>
        <div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPlayerModalLabel">
                            <i class="fas fa-user-plus me-2"></i> Adicionar Novo Jogador
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addPlayerForm">
                            <div class="mb-3">
                                <label for="newPlayerName" class="form-label">Nome do Jogador</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="newPlayerName" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="newPlayerType" class="form-label">Tipo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select" id="newPlayerType" required>
                                        <option value="Goleiro">Goleiro</option>
                                        <option value="Sim">Mensalista</option>
                                        <option value="Não">Não Mensalista</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-primary" id="saveNewPlayer">
                            <i class="fas fa-save me-1"></i> Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Jogador -->
        <div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPlayerModalLabel">
                            <i class="fas fa-user-edit me-2"></i> Editar Jogador
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPlayerForm">
                            <input type="hidden" id="editOldName">
                            <div class="mb-3">
                                <label for="editPlayerName" class="form-label">Nome do Jogador</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="editPlayerName" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editPlayerType" class="form-label">Tipo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <select class="form-select" id="editPlayerType" required>
                                        <option value="Goleiro">Goleiro</option>
                                        <option value="Sim">Mensalista</option>
                                        <option value="Não">Não Mensalista</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-primary" id="saveEditPlayer">
                            <i class="fas fa-save me-1"></i> Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Upload de Fotos -->
        <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadPhotoModalLabel">
                            <i class="fas fa-upload me-2"></i> Upload de Fotos
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadPhotoForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="photoFile" class="form-label">Selecione a foto</label>
                                <input type="file" class="form-control" id="photoFile" name="photo" accept="image/*" required>
                                <div class="form-text">Tamanho máximo: 5MB. Formatos: JPG, PNG, GIF</div>
                            </div>
                            <div class="mb-3">
                                <label for="photoCaption" class="form-label">Legenda</label>
                                <input type="text" class="form-control" id="photoCaption" name="caption" placeholder="Ex: Jogo contra Juventude - 10/03/2025">
                            </div>
                            <div class="mb-3">
                                <label for="photoDate" class="form-label">Data</label>
                                <input type="date" class="form-control" id="photoDate" name="date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-primary" id="savePhotoUpload">
                            <i class="fas fa-upload me-1"></i> Enviar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação para Excluir Foto -->
        <div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-labelledby="deletePhotoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deletePhotoModalLabel">
                            <i class="fas fa-trash-alt me-2"></i> Excluir Foto
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir esta foto? Esta ação não pode ser desfeita.</p>
                        <input type="hidden" id="deletePhotoId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDeletePhoto">
                            <i class="fas fa-trash-alt me-1"></i> Sim, Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal para Atualizar Jogo -->
    <?php if ($isAdmin): ?>
        <div class="modal fade" id="updateGameModal" tabindex="-1" aria-labelledby="updateGameModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateGameModalLabel">
                            <i class="fas fa-futbol me-2"></i> Atualizar Informações do Jogo
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="gameForm">
                            <div class="mb-3">
                                <label for="locationInput" class="form-label">Local</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control" id="locationInput" value="Arena Bom de Bola">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="dateInput" class="form-label">Data (Próxima Quarta)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control" id="dateInput" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="timeInput" class="form-label">Horário</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="text" class="form-control" id="timeInput" value="22:00">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-primary" id="saveGameInfo">
                            <i class="fas fa-save me-1"></i> Salvar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação de Reset Mensal -->
        <div class="modal fade" id="resetMonthModal" tabindex="-1" aria-labelledby="resetMonthModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="resetMonthModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i> Confirmação de Reset
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold">Atenção! Esta ação irá:</p>
                        <ul>
                            <li>Resetar todos os pagamentos para "Pendente"</li>
                            <li>Manter "Isentos" como estão</li>
                            <li>Esta ação não pode ser desfeita</li>
                        </ul>
                        <p>Tem certeza que deseja continuar?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmResetMonth">
                            <i class="fas fa-check me-1"></i> Sim, resetar mês
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-bell me-2"></i>
                <strong class="me-auto">Notificação</strong>
                <small class="text-muted" id="toastTime"></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <footer class="text-white text-center py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <h5>Destreinados Futebol Clube</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Local habitual: Arena Bom de Bola</p>
                    <p><i class="fas fa-calendar-alt me-2"></i> Quartas-feiras às 22h</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>© 2025 Destreinados Futebol Clube</p>
                    <p>Desenvolvido por Luis Miguel</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script>
        // Verificar se o usuário é admin para adicionar variável JS
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;

        // Função para alternar o modo escuro
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDarkMode = html.classList.toggle('dark-mode');

            // Alterar o ícone do botão
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.innerHTML = isDarkMode ?
                    '<i class="fas fa-sun"></i>' :
                    '<i class="fas fa-moon"></i>';
            }

            // Salvar a preferência do usuário
            document.cookie = `darkMode=${isDarkMode}; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/`;
        }

        // Função para obter a próxima quarta-feira
        function getNextWednesday() {
            const today = new Date();
            const dayOfWeek = today.getDay(); // 0 = Domingo, 1 = Segunda, ..., 3 = Quarta

            // Calcular dias até a próxima quarta
            let daysUntilWednesday = (3 - dayOfWeek + 7) % 7;

            // Se hoje é quarta-feira (dayOfWeek === 3) e ainda não passou das 22h, a próxima é hoje
            if (dayOfWeek === 3 && today.getHours() < 22) {
                daysUntilWednesday = 0;
            }

            const nextWednesday = new Date(today);
            nextWednesday.setDate(today.getDate() + daysUntilWednesday);

            const months = [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];

            return `${nextWednesday.getDate()} de ${months[nextWednesday.getMonth()]} de ${nextWednesday.getFullYear()}`;
        }

        // Função para mostrar toast
        function showToast(message) {
            const toast = new bootstrap.Toast(document.getElementById('actionToast'));
            document.querySelector('#actionToast .toast-body').textContent = message;
            document.getElementById('toastTime').textContent = new Date().toLocaleTimeString();
            toast.show();
        }

        // Função para carregar informações do jogo
        function loadGameInfo() {
            fetch('get_game_info.php')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success === false) throw new Error(data.error);

                    // Definir valores padrão caso não venham da API
                    const nextWednesday = getNextWednesday();
                    const defaultLocation = 'Arena Bom de Bola';
                    const defaultTime = '22:00';

                    // Atualizar elementos com os dados do jogo
                    document.getElementById('gameLocation').textContent = data.location || defaultLocation;
                    document.getElementById('gameDate').textContent = data.game_date || nextWednesday;
                    document.getElementById('gameTime').textContent = data.game_time || defaultTime;

                    // Também atualizar os campos do formulário, se existirem
                    if (document.getElementById('locationInput')) {
                        document.getElementById('locationInput').value = data.location || defaultLocation;
                        document.getElementById('dateInput').value = data.game_date || nextWednesday;
                        document.getElementById('timeInput').value = data.game_time || defaultTime;
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar game info:', error);

                    // Em caso de erro, usar valores padrão
                    const nextWednesday = getNextWednesday();
                    document.getElementById('gameLocation').textContent = 'Arena Bom de Bola';
                    document.getElementById('gameDate').textContent = nextWednesday;
                    document.getElementById('gameTime').textContent = '22:00';
                });
        }

        // Função para carregar status de pagamento
        function loadPaymentStatus() {
            fetch('get_payments.php')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success === false) throw new Error(data.error);

                    // Atualizar os status de pagamento na tabela
                    document.querySelectorAll('#paymentTable tr').forEach(row => {
                        const playerCell = row.querySelector('td:first-child');
                        if (!playerCell) return;

                        const player = playerCell.textContent.trim();
                        const statusCell = row.querySelector('td:nth-child(3)');

                        if (statusCell && data.data[player]) {
                            const statusText = data.data[player];

                            // Atualizar o badge de status
                            const statusBadge = statusCell.querySelector('.status-badge');
                            if (statusBadge) {
                                statusBadge.textContent = statusText;
                                statusBadge.className = 'status-badge';

                                if (statusText === 'OK') {
                                    statusBadge.classList.add('status-ok');
                                    statusBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> OK';
                                } else if (statusText === 'Pendente') {
                                    statusBadge.classList.add('status-pendente');
                                    statusBadge.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i> Pendente';
                                } else {
                                    statusBadge.classList.add('status-isento');
                                    statusBadge.innerHTML = '<i class="fas fa-shield-alt me-1"></i> Isento';
                                }
                            }
                        }
                    });

                    loadPlayers();
                    updateStatistics(); // Atualizar estatísticas após carregar pagamentos
                })
                .catch(error => console.error('Erro ao carregar pagamentos:', error));
        }

        // Função para carregar jogadores
        function loadPlayers() {
            const playersData = Array.from(document.querySelectorAll('#paymentTable tr')).map(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 3) return null;

                const name = cells[0].textContent.trim();

                // Obter tipo do jogador (Goleiro, Sim, Não)
                let type = 'Não';
                const badgeEl = cells[1].querySelector('.badge');
                if (badgeEl) {
                    const badgeText = badgeEl.textContent.trim();
                    if (badgeText.includes('Goleiro')) type = 'Goleiro';
                    else if (badgeText.includes('Sim')) type = 'Sim';
                }

                // Obter status do pagamento
                let status = 'Pendente';
                const statusEl = cells[2].querySelector('.status-badge');
                if (statusEl) {
                    const statusText = statusEl.textContent.trim();
                    status = statusText;
                }

                return {
                    name,
                    type,
                    status
                };
            }).filter(Boolean); // Remover nulos

            // Organizar jogadores por tipo
            const goalkeepers = playersData.filter(p => p.type === 'Goleiro');
            const monthlyPlayers = playersData.filter(p => p.type === 'Sim');
            const nonMonthlyPlayers = playersData.filter(p => p.type === 'Não');

            // Ordenar jogadores: Goleiros -> Mensalistas -> Não Mensalistas
            const sortedPlayers = [...goalkeepers, ...monthlyPlayers, ...nonMonthlyPlayers];

            // Dividir em 4 colunas
            const playersPerColumn = Math.ceil(sortedPlayers.length / 4);
            const columns = [
                [],
                [],
                [],
                []
            ];

            sortedPlayers.forEach((player, index) => {
                const columnIndex = Math.floor(index / playersPerColumn);
                if (columnIndex < 4) columns[columnIndex].push(player);
            });

            // Gerar HTML para cada coluna
            const playersList = document.getElementById('playersList');
            playersList.innerHTML = columns.map(column => `
                <div class="col-md-3">
                    <ul class="list-group">
                        ${column.map(player => `
                            <li class="list-group-item">
                                ${player.name} 
                                <small class="text-muted">(${player.type})</small>
                                <span class="${player.status === 'OK' ? 'player-paid' : player.status === 'Pendente' ? 'player-pending' : 'player-exempt'}">
                                    ${player.status}
                                </span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `).join('');
        }

        // Função para atualizar estatísticas
        function updateStatistics() {
            const rows = document.querySelectorAll('#paymentTable tr');

            let totalPlayers = 0;
            let paidPlayers = 0;
            let goalkeepers = 0;
            let monthlyPlayers = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 3) return; // Ignorar cabeçalhos ou linhas incompletas

                totalPlayers++;

                // Verificar status de pagamento
                const statusBadge = cells[2].querySelector('.status-badge');
                if (statusBadge && statusBadge.textContent.trim().includes('OK')) {
                    paidPlayers++;
                }

                // Verificar tipo de jogador
                const typeBadge = cells[1].querySelector('.badge');
                if (typeBadge) {
                    const badgeText = typeBadge.textContent.trim();
                    if (badgeText.includes('Goleiro')) {
                        goalkeepers++;
                    } else if (badgeText.includes('Sim')) {
                        monthlyPlayers++;
                    }
                }
            });

            // Calcular porcentagem de pagamento
            const paymentPercentage = totalPlayers > 0 ? Math.round((paidPlayers / totalPlayers) * 100) : 0;

            // Atualizar exibição das estatísticas
            document.getElementById('total-players').textContent = totalPlayers;
            document.getElementById('payment-percentage').textContent = paymentPercentage + '%';
            document.getElementById('total-goalkeepers').textContent = goalkeepers;
            document.getElementById('total-monthly').textContent = monthlyPlayers;
        }

        // Função para carregar galeria de fotos com datas corrigidas
        function loadGallery() {
            fetch('get_gallery.php')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    const galleryContent = document.getElementById('galleryContent');

                    if (!data.success || !data.photos || data.photos.length === 0) {
                        galleryContent.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-images fa-3x mb-3 text-muted"></i>
                        <h5>Nenhuma foto disponível</h5>
                        <p class="text-muted">As fotos dos jogos aparecerão aqui.</p>
                    </div>
                `;
                        return;
                    }

                    // Gerar HTML para a galeria
                    galleryContent.innerHTML = data.photos.map(photo => `
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="gallery-item position-relative" data-src="uploads/gallery/${photo.filename}">
                        <img src="uploads/gallery/${photo.filename}" alt="${photo.caption || 'Destreinados FC'}" loading="lazy">
                        <div class="gallery-caption">
                            ${photo.caption || ''} 
                            <small class="d-block">${photo.formatted_date || formatDate(photo.date)}</small>
                        </div>
                        ${isAdmin ? `
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 delete-photo" 
                                data-photo-id="${photo.id}" type="button">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');

                    // Inicializar o lightbox
                    if (window.lgInstance) {
                        window.lgInstance.destroy();
                    }

                    window.lgInstance = lightGallery(document.getElementById('galleryContent'), {
                        selector: '.gallery-item',
                        plugins: [lgZoom, lgThumbnail],
                        speed: 500,
                        download: false,
                        counter: true,
                        slideShowAutoplay: true
                    });

                    // Adicionar eventos aos botões de exclusão
                    if (isAdmin) {
                        document.querySelectorAll('.delete-photo').forEach(button => {
                            button.addEventListener('click', function(e) {
                                e.stopPropagation(); // Impedir que o lightbox abra
                                const photoId = this.getAttribute('data-photo-id');
                                document.getElementById('deletePhotoId').value = photoId;
                                const deleteModal = new bootstrap.Modal(document.getElementById('deletePhotoModal'));
                                deleteModal.show();
                            });
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar galeria:', error);
                    const galleryContent = document.getElementById('galleryContent');
                    galleryContent.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-exclamation-circle fa-3x mb-3 text-danger"></i>
                    <h5>Erro ao carregar galeria</h5>
                    <p class="text-muted">Ocorreu um erro ao carregar as fotos. Por favor, tente novamente mais tarde.</p>
                </div>
            `;
                });
        }

        // Função auxiliar para formatar datas
        function formatDate(dateString) {
            if (!dateString) return '';

            try {
                // Verifica se já está no formato dd/mm/yyyy
                if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateString)) {
                    return dateString;
                }

                // Se for no formato yyyy-mm-dd, converte para dd/mm/yyyy
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
                    const parts = dateString.split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }

                // Tenta formatar usando o objeto Date
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;

                // Retorna no formato brasileiro
                return date.toLocaleDateString('pt-BR');
            } catch (e) {
                console.error("Erro ao formatar data:", e);
                return dateString;
            }
        }
        // Função para formatar data no padrão brasileiro
        function formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            // Verificar se a data é válida
            if (isNaN(date.getTime())) return dateString;

            // Formatar para DD/MM/YYYY
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                timeZone: 'America/Sao_Paulo' // Usar o fuso horário brasileiro
            });
        }

        // Função para atualizar pagamento
        function updatePayment(player, newStatus) {
            fetch('update_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `player=${encodeURIComponent(player)}&status=${encodeURIComponent(newStatus)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadPaymentStatus();
                        showToast(`Status de ${player} atualizado para ${newStatus}!`);
                    } else {
                        alert('Erro ao atualizar pagamento: ' + (data.error || 'Desconhecido'));
                    }
                })
                .catch(error => console.error('Erro ao atualizar pagamento:', error));
        }

        // Função para excluir jogador
        function deletePlayer(player) {
            if (confirm(`Tem certeza que deseja excluir ${player}?`)) {
                fetch('delete_player.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `player_name=${encodeURIComponent(player)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadPaymentStatus();
                            showToast(`${player} excluído com sucesso!`);
                        } else {
                            alert('Erro ao excluir jogador: ' + (data.error || 'Desconhecido'));
                        }
                    })
                    .catch(error => console.error('Erro ao excluir jogador:', error));
            }
        }

        // Inicialização quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', () => {
            // Configurar o botão de alternância do modo escuro
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', toggleDarkMode);
            }

            // Carregar informações do jogo
            loadGameInfo();

            // Carregar status de pagamento
            loadPaymentStatus();

            // Configurar evento para a aba da galeria
            document.getElementById('gallery-tab').addEventListener('shown.bs.tab', function(e) {
                loadGallery();
            });

            // Configurar evento para o formulário de login
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;

                    fetch('login.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Usuário ou senha incorretos!');
                            }
                        })
                        .catch(error => console.error('Erro ao fazer login:', error));
                });
            }

            // Configurar evento para logout
            const logoutBtn = document.getElementById('logout');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    fetch('logout.php')
                        .then(() => location.reload())
                        .catch(error => console.error('Erro ao sair:', error));
                });
            }

            // Configurar eventos para alterar pagamento
            document.querySelectorAll('.toggle-payment').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const statusBadge = row.querySelector('.status-badge');
                    const player = row.querySelector('td:first-child').textContent.trim();
                    const type = this.getAttribute('data-type');
                    const currentStatus = statusBadge.textContent.trim();

                    let newStatus;
                    if (type === 'Goleiro') {
                        newStatus = currentStatus.includes('OK') ? 'Pendente' :
                            currentStatus.includes('Pendente') ? 'Isento' : 'OK';
                    } else {
                        newStatus = currentStatus.includes('OK') ? 'Pendente' : 'OK';
                    }

                    updatePayment(player, newStatus);
                });
            });

            // Configurar evento para salvar informações do jogo
            const saveGameInfoBtn = document.getElementById('saveGameInfo');
            if (saveGameInfoBtn) {
                saveGameInfoBtn.addEventListener('click', function() {
                    const location = document.getElementById('locationInput').value;
                    const date = document.getElementById('dateInput').value;
                    const time = document.getElementById('timeInput').value;

                    fetch('update_game_info.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `location=${encodeURIComponent(location)}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('gameLocation').textContent = location;
                                document.getElementById('gameDate').textContent = date;
                                document.getElementById('gameTime').textContent = time;

                                const modal = bootstrap.Modal.getInstance(document.getElementById('updateGameModal'));
                                modal.hide();

                                showToast('Jogo atualizado com sucesso!');
                            } else {
                                alert('Erro ao atualizar jogo: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(error => console.error('Erro ao atualizar jogo:', error));
                });
            }

            // Configurar evento para adicionar novo jogador
            const saveNewPlayerBtn = document.getElementById('saveNewPlayer');
            if (saveNewPlayerBtn) {
                saveNewPlayerBtn.addEventListener('click', function() {
                    const playerName = document.getElementById('newPlayerName').value.trim();
                    const playerType = document.getElementById('newPlayerType').value;

                    if (!playerName) {
                        alert('O nome do jogador não pode estar vazio!');
                        return;
                    }

                    fetch('add_player.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `player_name=${encodeURIComponent(playerName)}&type=${encodeURIComponent(playerType)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addPlayerModal'));
                                modal.hide();
                                loadPaymentStatus();
                                document.getElementById('addPlayerForm').reset();
                                showToast('Jogador adicionado com sucesso!');
                            } else {
                                alert('Erro ao adicionar jogador: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(error => console.error('Erro ao adicionar jogador:', error));
                });
            }

            // Configurar eventos para editar jogador
            document.querySelectorAll('.edit-player').forEach(button => {
                button.addEventListener('click', function() {
                    const playerName = this.getAttribute('data-player');
                    const playerType = this.getAttribute('data-type');
                    document.getElementById('editOldName').value = playerName;
                    document.getElementById('editPlayerName').value = playerName;
                    document.getElementById('editPlayerType').value = playerType;
                });
            });

            // Configurar evento para salvar jogador editado
            const saveEditPlayerBtn = document.getElementById('saveEditPlayer');
            if (saveEditPlayerBtn) {
                saveEditPlayerBtn.addEventListener('click', function() {
                    const oldName = document.getElementById('editOldName').value;
                    const playerName = document.getElementById('editPlayerName').value.trim();
                    const playerType = document.getElementById('editPlayerType').value;

                    if (!playerName) {
                        alert('O nome do jogador não pode estar vazio!');
                        return;
                    }

                    fetch('edit_player.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `old_name=${encodeURIComponent(oldName)}&player_name=${encodeURIComponent(playerName)}&type=${encodeURIComponent(playerType)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('editPlayerModal'));
                                modal.hide();
                                loadPaymentStatus();
                                showToast('Jogador editado com sucesso!');
                            } else {
                                alert('Erro ao editar jogador: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(error => console.error('Erro ao editar jogador:', error));
                });
            }

            // Configurar evento para upload de foto com melhor tratamento de erros
            const savePhotoUploadBtn = document.getElementById('savePhotoUpload');
            if (savePhotoUploadBtn) {
                savePhotoUploadBtn.addEventListener('click', function() {
                    const photoFile = document.getElementById('photoFile').files[0];
                    const photoCaption = document.getElementById('photoCaption').value;
                    const photoDate = document.getElementById('photoDate').value;

                    if (!photoFile) {
                        alert('Selecione uma foto para upload!');
                        return;
                    }

                    // Mostrar mensagem de carregamento
                    savePhotoUploadBtn.disabled = true;
                    savePhotoUploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

                    const formData = new FormData();
                    formData.append('photo', photoFile);
                    formData.append('caption', photoCaption);
                    formData.append('date', photoDate);

                    fetch('upload_photo.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            console.log('Resposta bruta:', response);
                            return response.text(); // Obter texto primeiro para debugging
                        })
                        .then(text => {
                            console.log('Texto de resposta:', text);
                            // Tentar converter para JSON
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Erro ao converter resposta para JSON:', e);
                                throw new Error('Resposta inválida do servidor');
                            }
                        })
                        .then(data => {
                            savePhotoUploadBtn.disabled = false;
                            savePhotoUploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Enviar';

                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadPhotoModal'));
                                modal.hide();
                                document.getElementById('uploadPhotoForm').reset();
                                showToast('Foto enviada com sucesso!');

                                // Recarregar a galeria se a aba estiver ativa
                                const galleryTab = document.querySelector('#gallery-tab.active');
                                if (galleryTab) {
                                    loadGallery();
                                }
                            } else {
                                alert('Erro ao enviar foto: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(error => {
                            console.error('Erro completo:', error);
                            savePhotoUploadBtn.disabled = false;
                            savePhotoUploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Enviar';
                            alert('Erro ao enviar foto: ' + error.message);
                        });
                });
            }

            // Configurar evento para excluir foto
            const confirmDeletePhotoBtn = document.getElementById('confirmDeletePhoto');
            if (confirmDeletePhotoBtn) {
                confirmDeletePhotoBtn.addEventListener('click', function() {
                    const photoId = document.getElementById('deletePhotoId').value;
                    if (!photoId) return;

                    confirmDeletePhotoBtn.disabled = true;
                    confirmDeletePhotoBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';

                    fetch('delete_photo.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `photo_id=${encodeURIComponent(photoId)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            confirmDeletePhotoBtn.disabled = false;
                            confirmDeletePhotoBtn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Sim, Excluir';

                            const modal = bootstrap.Modal.getInstance(document.getElementById('deletePhotoModal'));
                            modal.hide();

                            if (data.success) {
                                loadGallery(); // Recarregar a galeria
                                showToast('Foto excluída com sucesso!');
                            } else {
                                alert('Erro ao excluir foto: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao excluir foto:', error);
                            confirmDeletePhotoBtn.disabled = false;
                            confirmDeletePhotoBtn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Sim, Excluir';
                            alert('Erro ao excluir foto: ' + error.message);
                        });
                });
            }

            // Configurar eventos para excluir jogador
            document.querySelectorAll('.delete-player').forEach(button => {
                button.addEventListener('click', function() {
                    const player = this.getAttribute('data-player');
                    deletePlayer(player);
                });
            });

            // Configurar evento para o botão de resetar mês
            const resetMonthBtn = document.getElementById('resetMonthBtn');
            if (resetMonthBtn) {
                resetMonthBtn.addEventListener('click', function() {
                    const resetMonthModal = new bootstrap.Modal(document.getElementById('resetMonthModal'));
                    resetMonthModal.show();
                });
            }

            // Configurar evento para confirmar reset do mês
            const confirmResetMonthBtn = document.getElementById('confirmResetMonth');
            if (confirmResetMonthBtn) {
                confirmResetMonthBtn.addEventListener('click', function() {
                    fetch('reset_month.php', {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('resetMonthModal'));
                                modal.hide();
                                loadPaymentStatus();
                                showToast('Mês resetado com sucesso!');
                            } else {
                                alert('Erro ao resetar mês: ' + (data.error || 'Desconhecido'));
                            }
                        })
                        .catch(error => console.error('Erro ao resetar mês:', error));
                });
            }

            // Configurar evento para exportar CSV
            const exportCSVBtn = document.getElementById('exportCSV');
            if (exportCSVBtn) {
                exportCSVBtn.addEventListener('click', function() {
                    window.location.href = 'export_csv.php';
                });
            }
        });
    </script>
</body>

</html>