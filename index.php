<?php
session_start();
$isAdmin = isset($_SESSION['admin']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destreinados Futebol Clube</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .player-paid { color: green; font-weight: bold; }
        .player-pending { color: red; }
        .login-form { max-width: 300px; }
        .admin-only { display: none; }
        .logged-in .admin-only { display: inline-block; }
    </style>
</head>
<body class="<?php echo $isAdmin ? 'logged-in' : ''; ?>">
    <header class="bg-dark text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Destreinados Futebol Clube</h1>
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
            <button class="btn btn-primary admin-only" data-bs-toggle="modal" data-bs-target="#updateGameModal">Atualizar Jogo</button>
        </section>

        <section class="mb-5">
            <h2>Jogadores</h2>
            <div class="row">
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item">1. Luis Miguel</li>
                        <li class="list-group-item">2. Francisco</li>
                        <li class="list-group-item">3. Ander</li>
                        <li class="list-group-item">4. Thiago</li>
                        <li class="list-group-item">5. Eduardo</li>
                        <li class="list-group-item">6. Cassio</li>
                        <li class="list-group-item">7. Fernando</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item">8. Claudio</li>
                        <li class="list-group-item">9. Paulo</li>
                        <li class="list-group-item">10. Felipe</li>
                        <li class="list-group-item">11. Valdecir</li>
                        <li class="list-group-item">12. Winderson</li>
                        <li class="list-group-item">13. Andrei</li>
                        <li class="list-group-item">14. Diogo</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item">15. Douglas</li>
                        <li class="list-group-item">16. Everson</li>
                        <li class="list-group-item">17. Ezequiel</li>
                        <li class="list-group-item">18. Formentão</li>
                        <li class="list-group-item">19. João Gustavo</li>
                        <li class="list-group-item">20. Kevin</li>
                        <li class="list-group-item">21. Leonardo</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul class="list-group">
                        <li class="list-group-item">22. Lucas</li>
                        <li class="list-group-item">23. Luis Azevedo</li>
                        <li class="list-group-item">24. Pedro</li>
                        <li class="list-group-item">25. Tiago</li>
                        <li class="list-group-item">26. Alan</li>
                        <li class="list-group-item">27. Convidado</li>
                        <li class="list-group-item">28. Convidado</li>
                    </ul>
                </div>
            </div>
        </section>

        <section>
            <h2>Lista de Pagamentos - <?php 
                $months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
                echo $months[date('n') - 1] . ' ' . date('Y');
            ?></h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Jogador</th>
                        <th>Mensalista</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody id="paymentTable">
                    <tr><td>Luis Miguel</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Luis Miguel">Alterar</button></td></tr>
                    <tr><td>Francisco</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Francisco">Alterar</button></td></tr>
                    <tr><td>Ander</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Ander">Alterar</button></td></tr>
                    <tr><td>Thiago</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Thiago">Alterar</button></td></tr>
                    <tr><td>Eduardo</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Eduardo">Alterar</button></td></tr>
                    <tr><td>Cassio</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Cassio">Alterar</button></td></tr>
                    <tr><td>Fernando</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Fernando">Alterar</button></td></tr>
                    <tr><td>Claudio</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Claudio">Alterar</button></td></tr>
                    <tr><td>Paulo</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Paulo">Alterar</button></td></tr>
                    <tr><td>Felipe</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Felipe">Alterar</button></td></tr>
                    <tr><td>Valdecir</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Valdecir">Alterar</button></td></tr>
                    <tr><td>Winderson</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Winderson">Alterar</button></td></tr>
                    <tr><td>Andrei</td><td>Goleiro</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Andrei">Alterar</button></td></tr>
                    <tr><td>Diogo</td><td>Goleiro</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Diogo">Alterar</button></td></tr>
                    <tr><td>Douglas</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Douglas">Alterar</button></td></tr>
                    <tr><td>Everson</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Everson">Alterar</button></td></tr>
                    <tr><td>Ezequiel</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Ezequiel">Alterar</button></td></tr>
                    <tr><td>Formentão</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Formentão">Alterar</button></td></tr>
                    <tr><td>João Gustavo</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="João Gustavo">Alterar</button></td></tr>
                    <tr><td>Kevin</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Kevin">Alterar</button></td></tr>
                    <tr><td>Leonardo</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Leonardo">Alterar</button></td></tr>
                    <tr><td>Lucas</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Lucas">Alterar</button></td></tr>
                    <tr><td>Luis Azevedo</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Luis Azevedo">Alterar</button></td></tr>
                    <tr><td>Pedro</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Pedro">Alterar</button></td></tr>
                    <tr><td>Tiago</td><td>Sim</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Tiago">Alterar</button></td></tr>
                    <tr><td>Alan</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Alan">Alterar</button></td></tr>
                    <tr><td>Convidado</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Convidado1">Alterar</button></td></tr>
                    <tr><td>Convidado</td><td>Não</td><td class="player-pending">Pendente</td><td><button class="btn btn-sm btn-warning toggle-payment admin-only" data-player="Convidado2">Alterar</button></td></tr>
                </tbody>
            </table>
        </section>
    </div>

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

    <footer class="bg-dark text-white text-center py-3">
        <p>© 2025 Destreinados Futebol Clube</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const adminUsername = "admin";
        const adminPassword = "destreinados123";

        function getNextWednesday() {
            const today = new Date();
            const dayOfWeek = today.getDay();
            const daysUntilWednesday = (3 - dayOfWeek + 7) % 7 || 7;
            const nextWednesday = new Date(today);
            nextWednesday.setDate(today.getDate() + daysUntilWednesday);
            const months = [
                'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
            ];
            return `${nextWednesday.getDate()} de ${months[nextWednesday.getMonth()]} de ${nextWednesday.getFullYear()}`;
        }

        function loadGameInfo() {
            fetch('get_game_info.php')
                .then(response => {
                    if (!response.ok) {
                        console.error('Erro na requisição get_game_info:', response.status);
                        throw new Error('Erro na resposta: ' + response.status);
                    }
                    return response.text();
                })
                .then(text => {
                    console.log('Resposta bruta de get_game_info:', text);
                    const data = JSON.parse(text);
                    if (data.success === false) throw new Error(data.error);
                    document.getElementById('gameLocation').textContent = data.location || 'Arena Bom de Bola';
                    document.getElementById('gameDate').textContent = data.game_date || getNextWednesday();
                    document.getElementById('gameTime').textContent = data.game_time || '22:00';
                    document.getElementById('locationInput').value = data.location || 'Arena Bom de Bola';
                    document.getElementById('dateInput').value = data.game_date || getNextWednesday();
                    document.getElementById('timeInput').value = data.game_time || '22:00';
                })
                .catch(error => {
                    console.error('Erro ao carregar game info:', error);
                    document.getElementById('gameLocation').textContent = 'Erro ao carregar';
                    document.getElementById('gameDate').textContent = getNextWednesday(); // Fallback para data
                    document.getElementById('timeInput').value = '22:00'; // Fallback para horário
                });
        }

        function loadPaymentStatus() {
            fetch('get_payments.php')
                .then(response => {
                    if (!response.ok) {
                        console.error('Erro na requisição get_payments:', response.status);
                        throw new Error('Erro na resposta: ' + response.status);
                    }
                    return response.text();
                })
                .then(text => {
                    console.log('Resposta bruta de get_payments:', text);
                    const data = JSON.parse(text);
                    if (data.success === false) throw new Error(data.error);
                    document.querySelectorAll('#paymentTable tr').forEach(row => {
                        const player = row.querySelector('td:first-child').textContent.trim();
                        const statusCell = row.querySelector('td:nth-child(3)');
                        if (data[player]) {
                            statusCell.textContent = data[player];
                            statusCell.className = data[player] === 'OK' ? 'player-paid' : 'player-pending';
                        }
                    });
                })
                .catch(error => console.error('Erro ao carregar pagamentos:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadGameInfo();
            loadPaymentStatus();
            document.getElementById('dateInput').value = getNextWednesday(); // Define a data padrão no modal
        });

        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            fetch('login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
                const newStatus = statusCell.textContent === 'OK' ? 'Pendente' : 'OK';

                fetch('update_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `player=${encodeURIComponent(player)}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusCell.textContent = newStatus;
                        statusCell.className = newStatus === 'OK' ? 'player-paid' : 'player-pending';
                    } else {
                        alert('Erro ao atualizar pagamento: ' + (data.error || 'Desconhecido'));
                    }
                })
                .catch(error => console.error('Erro ao atualizar pagamento:', error));
            });
        });

        document.getElementById('saveGameInfo').addEventListener('click', function() {
            const location = document.getElementById('locationInput').value;
            const date = getNextWednesday();
            const time = document.getElementById('timeInput').value;

            fetch('update_game_info.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `location=${encodeURIComponent(location)}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`
            })
            .then(response => {
                console.log('update_game_info Status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('update_game_info Resposta:', text);
                const data = JSON.parse(text);
                if (data.success) {
                    document.getElementById('gameLocation').textContent = location;
                    document.getElementById('gameDate').textContent = date;
                    document.getElementById('gameTime').textContent = time;
                    document.getElementById('locationInput').value = location;
                    document.getElementById('dateInput').value = date;
                    document.getElementById('timeInput').value = time;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateGameModal'));
                    modal.hide();
                } else {
                    alert('Erro ao atualizar jogo: ' + (data.error || 'Desconhecido'));
                }
            })
            .catch(error => console.error('Erro ao atualizar jogo:', error));
        });
    </script>
</body>
</html>