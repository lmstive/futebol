<?php
session_start();
$isAdmin = isset($_SESSION['admin']);
require_once 'config.php';

$stmt = $pdo->query("SELECT id, filename FROM photos ORDER BY id DESC");
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotos - Lendários FC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header { background-color: #2c3e50; }
        footer { background-color: #2c3e50; }
        h2 { color: #27ae60; }
        .gallery { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
        .photo-card { width: 300px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s; position: relative; }
        .photo-card:hover { transform: scale(1.05); }
        .photo-img { width: 100%; height: 200px; object-fit: cover; cursor: pointer; }
        .delete-btn { position: absolute; top: 10px; right: 10px; background: rgba(255, 0, 0, 0.8); border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        .delete-btn:hover { background: rgba(200, 0, 0, 1); }
        .modal-img { max-width: 100%; max-height: 70vh; object-fit: contain; }
        .modal-footer { justify-content: space-between; }
    </style>
</head>
<body>
    <header class="text-white py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="logo_lendarios.png" alt="Logo Lendários FC" style="max-height: 50px; margin-right: 10px;">
                <h1 class="h3 mb-0">Lendários FC</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                <nav>
                    <a href="index.php" class="btn btn-outline-light btn-md me-2" style="padding: 8px 16px;">Início</a>
                    <a href="photos.php" class="btn btn-outline-light btn-md" style="padding: 8px 16px;">Fotos</a>
                </nav>
                <?php if (!$isAdmin): ?>
                <form id="loginForm" class="login-form d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" id="username" placeholder="Usuário" required>
                    <input type="password" class="form-control form-control-sm" id="password" placeholder="Senha" required>
                    <button type="submit" class="btn btn-primary btn-md">Entrar</button>
                </form>
                <?php else: ?>
                <div class="text-white d-flex align-items-center gap-2">
                    <span>Admin Logado</span>
                    <button class="btn btn-danger btn-md" id="logout">Sair</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container my-5">
        <h2>Fotos</h2>
        <?php if ($isAdmin): ?>
        <form id="uploadForm" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="photo" class="form-label">Escolher Foto</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Foto</button>
        </form>
        <?php endif; ?>

        <div class="gallery" id="photoGallery">
            <?php foreach ($photos as $photo): ?>
            <div class="photo-card">
                <img src="uploads/<?php echo htmlspecialchars($photo['filename']); ?>" alt="Foto" class="photo-img" data-bs-toggle="modal" data-bs-target="#photoModal" data-id="<?php echo $photo['id']; ?>" data-src="uploads/<?php echo htmlspecialchars($photo['filename']); ?>">
                <?php if ($isAdmin): ?>
                <button class="delete-btn" data-photo-id="<?php echo $photo['id']; ?>">Excluir</button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para visualização da foto -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Visualizar Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Foto Ampliada" class="modal-img">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevPhoto">Anterior</button>
                    <button type="button" class="btn btn-primary" id="nextPhoto">Próxima</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-white text-center py-3">
        <p>© 2025 Lendários FC</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const photos = <?php echo json_encode(array_map(function($photo) {
            return ['id' => $photo['id'], 'src' => 'uploads/' . htmlspecialchars($photo['filename'])];
        }, $photos)); ?>;

        <?php if ($isAdmin): ?>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('upload_photo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Erro: ' + data.error);
                }
            })
            .catch(error => console.error('Erro ao enviar foto:', error));
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                if (confirm('Tem certeza que deseja excluir esta foto?')) {
                    const photoId = this.getAttribute('data-photo-id');

                    fetch('delete_photo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `photo_id=${encodeURIComponent(photoId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Erro: ' + data.error);
                        }
                    })
                    .catch(error => console.error('Erro ao excluir foto:', error));
                }
            });
        });
        <?php endif; ?>

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

        // Modal para ampliar foto com navegação
        let currentPhotoIndex = 0;

        document.querySelectorAll('.photo-img').forEach(img => {
            img.addEventListener('click', function() {
                const photoId = parseInt(this.getAttribute('data-id'));
                currentPhotoIndex = photos.findIndex(photo => photo.id === photoId);
                updateModalImage();
            });
        });

        function updateModalImage() {
            if (currentPhotoIndex >= 0 && currentPhotoIndex < photos.length) {
                document.getElementById('modalImage').src = photos[currentPhotoIndex].src;
            }
        }

        document.getElementById('nextPhoto').addEventListener('click', function() {
            if (currentPhotoIndex < photos.length - 1) {
                currentPhotoIndex++;
                updateModalImage();
            }
        });

        document.getElementById('prevPhoto').addEventListener('click', function() {
            if (currentPhotoIndex > 0) {
                currentPhotoIndex--;
                updateModalImage();
            }
        });
    </script>
</body>
</html>