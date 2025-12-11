<?php
session_start();
require 'db.php';

// Segurança: se não estiver logado, tchau
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Pega dados do usuário
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 2. Pega APENAS os posts desse usuário
$posts_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY id DESC");
$posts_stmt->bind_param("i", $user_id);
$posts_stmt->execute();
$my_posts = $posts_stmt->get_result();

// Conta quantos posts tem
$num_posts = $my_posts->num_rows;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo $user['username']; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #181818;
            --card-bg: #252525;
            --brand-yellow: #FFCC00;
            --text-main: #ffffff;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            padding-top: 80px;
        }

        .navbar {
            background-color: var(--bg-color);
            border-bottom: 1px solid #333;
            padding: 10px 20px;
        }
        
        .brand-logo { font-weight: 900; font-size: 1.5rem; color: white; text-decoration: none; text-transform: uppercase; }
        .brand-logo span { color: var(--brand-yellow); }

        .profile-header { text-align: center; margin-bottom: 30px; }
        .avatar-container { width: 120px; height: 120px; margin: 0 auto 15px; }
        .profile-pic { width: 100%; height: 100%; border-radius: 50%; border: 3px solid var(--brand-yellow); object-fit: cover; }

        .profile-username { font-size: 1.5rem; font-weight: bold; }
        .profile-bio { color: #aaa; font-size: 0.95rem; }

        .nav-tabs { border-bottom: 1px solid #333; }
        .nav-link { color: #888; border: none; font-weight: 600; padding-bottom: 15px; }
        .nav-link:hover { color: white; }
        .nav-link.active { color: var(--brand-yellow); background: transparent; border-bottom: 3px solid var(--brand-yellow); }

        .meme-grid-item {
            position: relative;
            aspect-ratio: 1/1;
            background-color: #222;
            overflow: hidden;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .meme-grid-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .meme-grid-item:hover img { transform: scale(1.05); }
    </style>
</head>
<body>

    <nav class="navbar fixed-top">
        <div class="container-fluid">
            <a href="index.php" class="brand-logo ms-2">Libre<span>Funny</span></a>
            <a href="index.php" class="btn btn-outline-light btn-sm">Voltar ao Feed</a>
        </div>
    </nav>

    <div class="container mt-4">
        
        <div class="profile-header">
            <div class="avatar-container">
                <img src="<?php echo $user['avatar_path'] ? $user['avatar_path'] : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $user['username']; ?>" class="profile-pic">
            </div>

            <h1 class="profile-username"><?php echo $user['username']; ?></h1>
            <p class="profile-bio"><?php echo $user['bio']; ?></p>
            
            <div class="d-flex justify-content-center gap-4 mt-3 text-muted">
                <span><strong class="text-white"><?php echo $num_posts; ?></strong> Memes</span>
                <span><strong class="text-white">0</strong> Seguidores</span>
            </div>
        </div>

        <ul class="nav nav-tabs nav-fill mb-4" id="profileTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#myposts">
                    <i class="fa-solid fa-image me-2"></i> Meus Posts
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#liked">
                    <i class="fa-solid fa-heart me-2"></i> Curtidos
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            <div class="tab-pane fade show active" id="myposts">
                <div class="row">
                    <?php if ($num_posts > 0): ?>
                        <?php while($post = $my_posts->fetch_assoc()): ?>
                            <div class="col-4 col-md-3">
                                <div class="meme-grid-item">
                                    <img src="<?php echo $post['image_path']; ?>" alt="Meme">
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">Você ainda não postou nada.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="liked">
                <p class="text-center text-muted mt-5">Em breve você verá seus memes curtidos aqui.</p>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>