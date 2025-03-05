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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destreinados Futebol Clube</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .player-paid {
            color: green;
            font-weight: bold;
        }

        .player-pending {
            color: red;
        }

        .player-exempt {
            color: blue;
            font-weight: bold;
        }

        .login-form {
            max-width: 300px;
        }

        header {
            background-color: #2c3e50;
        }

        .btn-success {
            background-color: #27ae60;
            border-color: #27ae60;
        }

        footer {
            background-color: #2c3e50;
        }

        h2 {
            color: #27ae60;
        }

        .logo {
            max-height: 50px;
            margin-right: 10px;
        }

        /* Estilo para a logo */
    </style>
</head>

<body>
    <header class="text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="logo.png" alt="Logo Destreinados FC" class="logo"> <!-- Logo adicionada aqui -->
                <h1 class="h3 mb-0">Destreinados Futebol Clube</h1>
            </div>
            <?php if (!$isAdmin): ?>
                <form id="loginForm" class="login-form d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" id="username" placeholder="Usuário" required>
                    <input type="password" class="form-control form-control-sm" id="password" placeholder="Senha" required>
                    <button type="submit" class="btn btn-sm btn-primary">Entrar</button>
                </form>
            <?php else: ?>
                <div class="text-white">Admin Logado <button class="btn btn-sm btn-danger" id="logout">Sair</button></div>
            <?php endif; ?>
        </div>
    </header>

    <div class="container my-5">
        <section class="mb-5" id="gameInfo">
            <h2>Informações do Próximo Jogo</h2>
            <p><strong>Local:</strong> <span id="gameLocation">Carregando...</span></p>
            <p><strong>Data:</strong> <span id="gameDate">Carregando...</span></p>
            <p><strong>Horário:</strong> <span id="gameTime">Carregando...</span></p>
            <?php if ($isAdmin): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateGameModal">Atualizar Jogo</button>
            <?php endif; ?>
        </section>

        <section class="mb-5">
            <h2>Jogadores</h2>
            <div class="row" id="playersList">
                <!-- Lista gerada dinamicamente pelo JavaScript -->
            </div>
        </section>

        <section>
            <h2>Lista de Pagamentos - <?php
                                        $months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
                                        echo $months[date('n') - 1] . ' ' . date('Y');
                                        ?></h2>
            <?php if ($isAdmin): ?>
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addPlayerModal">Adicionar Jogador</button>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Jogador</th>
                            <th>Mensalista</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="paymentTable">
                        <?php foreach ($sortedPlayers as $player): ?>
                            <tr>
                                <td><?php echo $player['player_name']; ?></td>
                                <td><?php echo $player['type']; ?></td>
                                <td class="<?php
                                            if ($player['status'] === 'OK') echo 'player-paid';
                                            elseif ($player['status'] === 'Pendente') echo 'player-pending';
                                            elseif ($player['status'] === 'Isento') echo 'player-exempt';
                                            ?>">
                                    <?php echo $player['status']; ?>
                                </td>
                                <td>
                                    <?php if ($isAdmin): ?>
                                        <button class="btn btn-sm btn-warning toggle-payment" data-player="<?php echo $player['player_name']; ?>" data-type="<?php echo $player['type']; ?>">Alterar</button>
                                        <button class="btn btn-sm btn-primary edit-player" data-player="<?php echo $player['player_name']; ?>" data-type="<?php echo $player['type']; ?>" data-bs-toggle="modal" data-bs-target="#editPlayerModal">Editar</button>
                                        <button class="btn btn-sm btn-danger delete-player" data-player="<?php echo $player['player_name']; ?>">Excluir</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($isAdmin): ?>
                <a href="report.php?month=<?php echo date('Y-m'); ?>" target="_blank" class="btn btn-info mt-3">Relatório Mensal</a>
            <?php endif; ?>
        </section>
    </div>

    <!-- Modal para Adicionar Jogador -->
    <?php if ($isAdmin): ?>
        <div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPlayerModalLabel">Adicionar Novo Jogador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addPlayerForm">
                            <div class="mb-3">
                                <label for="newPlayerName" class="form-label">Nome do Jogador</label>
                                <input type="text" class="form-control" id="newPlayerName" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPlayerType" class="form-label">Tipo</label>
                                <select class="form-select" id="newPlayerType" required>
                                    <option value="Goleiro">Goleiro</option>
                                    <option value="Sim">Mensalista</option>
                                    <option value="Não">Não Mensalista</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="saveNewPlayer">Salvar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Jogador -->
        <div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPlayerModalLabel">Editar Jogador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPlayerForm">
                            <input type="hidden" id="editOldName">
                            <div class="mb-3">
                                <label for="editPlayerName" class="form-label">Nome do Jogador</label>
                                <input type="text" class="form-control" id="editPlayerName" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPlayerType" class="form-label">Tipo</label>
                                <select class="form-select" id="editPlayerType" required>
                                    <option value="Goleiro">Goleiro</option>
                                    <option value="Sim">Mensalista</option>
                                    <option value="Não">Não Mensalista</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="saveEditPlayer">Salvar</button>
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
                        <h5 class="modal-title" id="updateGameModalLabel">Atualizar Informações do Jogo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="gameForm">
                            <div class="mb-3">
                                <label for="locationInput" class="form-label">Local</label>
                                <input type="text" class="form-control" id="locationInput" value="Arena Bom de Bola">
                            </div>
                            <div class="mb-3">
                                <label for="dateInput" class="form-label">Data (Próxima Quarta)</label>
                                <input type="text" class="form-control" id="dateInput" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="timeInput" class="form-label">Horário</label>
                                <input type="text" class="form-control" id="timeInput" value="22:00">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="saveGameInfo">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Ação</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <footer class="text-white text-center py-3">
        <p>© 2025 Destreinados Futebol Clube</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function getNextWednesday() {
            const today = new Date();
            const dayOfWeek = today.getDay(); // 0 = Domingo, 1 = Segunda, ..., 3 = Quarta
            const hours = today.getHours();
            const daysUntilWednesday = (3 - dayOfWeek + 7) % 7 || 7;

            // Se hoje é quarta-feira (dayOfWeek === 3) e ainda não passou das 22h, mantém a data de hoje
            if (dayOfWeek === 3 && hours < 22) {
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

        function showToast(message) {
            const toast = new bootstrap.Toast(document.getElementById('actionToast'));
            document.querySelector('#actionToast .toast-body').textContent = message;
            toast.show();
        }

        function loadGameInfo() {
            fetch('get_game_info.php')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success === false) throw new Error(data.error);
                    document.getElementById('gameLocation').textContent = data.location || 'Arena Bom de Bola';
                    document.getElementById('gameDate').textContent = data.game_date || getNextWednesday();
                    document.getElementById('gameTime').textContent = data.game_time || '22:00';
                    if (document.getElementById('locationInput')) {
                        document.getElementById('locationInput').value = data.location || 'Arena Bom de Bola';
                        document.getElementById('dateInput').value = data.game_date || getNextWednesday();
                        document.getElementById('timeInput').value = data.game_time || '22:00';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar game info:', error);
                    document.getElementById('gameLocation').textContent = 'Erro ao carregar';
                    document.getElementById('gameDate').textContent = getNextWednesday();
                    document.getElementById('gameTime').textContent = 'Erro ao carregar';
                });
        }

        function loadPaymentStatus() {
            fetch('get_payments.php')
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success === false) throw new Error(data.error);
                    document.querySelectorAll('#paymentTable tr').forEach(row => {
                        const player = row.querySelector('td:first-child').textContent.trim();
                        const statusCell = row.querySelector('td:nth-child(3)');
                        if (data.data[player]) {
                            statusCell.textContent = data.data[player];
                            statusCell.className = data.data[player] === 'OK' ? 'player-paid' :
                                data.data[player] === 'Pendente' ? 'player-pending' :
                                'player-exempt';
                        }
                    });
                    loadPlayers();
                })
                .catch(error => console.error('Erro ao carregar pagamentos:', error));
        }

        function loadPlayers() {
            const playersData = Array.from(document.querySelectorAll('#paymentTable tr')).map(row => {
                const cells = row.querySelectorAll('td');
                return {
                    name: cells[0].textContent.trim(),
                    type: cells[1].textContent.trim(),
                    status: cells[2].textContent.trim()
                };
            });

            const goalkeepers = playersData.filter(p => p.type === 'Goleiro');
            const monthlyPlayers = playersData.filter(p => p.type === 'Sim');
            const nonMonthlyPlayers = playersData.filter(p => p.type === 'Não');

            const sortedPlayers = [...goalkeepers, ...monthlyPlayers, ...nonMonthlyPlayers];
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

        document.addEventListener('DOMContentLoaded', () => {
            loadGameInfo();
            loadPaymentStatus();
        });

        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
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

        document.getElementById('logout')?.addEventListener('click', function() {
            fetch('logout.php')
                .then(() => location.reload())
                .catch(error => console.error('Erro ao sair:', error));
        });

        document.querySelectorAll('.toggle-payment').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const statusCell = row.querySelector('td:nth-child(3)');
                const player = row.querySelector('td:first-child').textContent.trim();
                const type = this.getAttribute('data-type');
                const currentStatus = statusCell.textContent.trim();

                let newStatus;
                if (type === 'Goleiro') {
                    newStatus = currentStatus === 'OK' ? 'Pendente' :
                        currentStatus === 'Pendente' ? 'Isento' : 'OK';
                } else {
                    newStatus = currentStatus === 'OK' ? 'Pendente' : 'OK';
                }

                updatePayment(player, newStatus);
            });
        });

        document.getElementById('saveGameInfo')?.addEventListener('click', function() {
            const location = document.getElementById('locationInput').value;
            const date = getNextWednesday();
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
                        document.getElementById('locationInput').value = location;
                        document.getElementById('dateInput').value = date;
                        document.getElementById('timeInput').value = time;
                        const modal = bootstrap.Modal.getInstance(document.getElementById('updateGameModal'));
                        modal.hide();
                        showToast('Jogo atualizado com sucesso!');
                    } else {
                        alert('Erro ao atualizar jogo: ' + (data.error || 'Desconhecido'));
                    }
                })
                .catch(error => console.error('Erro ao atualizar jogo:', error));
        });

        document.getElementById('saveNewPlayer')?.addEventListener('click', function() {
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

        document.querySelectorAll('.edit-player').forEach(button => {
            button.addEventListener('click', function() {
                const playerName = this.getAttribute('data-player');
                const playerType = this.getAttribute('data-type');
                document.getElementById('editOldName').value = playerName;
                document.getElementById('editPlayerName').value = playerName;
                document.getElementById('editPlayerType').value = playerType;
            });
        });

        document.getElementById('saveEditPlayer')?.addEventListener('click', function() {
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

        document.querySelectorAll('.delete-player').forEach(button => {
            button.addEventListener('click', function() {
                const player = this.getAttribute('data-player');
                deletePlayer(player);
            });
        });
    </script>
</body>

</html>
