<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPMB UPTD SD Negeri No. 071184 Tetesua</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/login-posters.css">
    <link rel="stylesheet" href="assets/css/login-playful-bg.css">
    <link rel="stylesheet" href="assets/css/app-credit.css">
</head>
<body class="login-page">
    <!-- Dekorasi latar: gradasi biru + ikon playful (bukan bagian form) -->
    <div class="login-playful-bg" aria-hidden="true">
        <span class="login-playful-icon login-playful-icon--books" title="">📚</span>
        <span class="login-playful-icon login-playful-icon--pencil" title="">✏️</span>
        <span class="login-playful-icon login-playful-icon--star" title="">⭐</span>
        <span class="login-playful-icon login-playful-icon--telescope" title="">🔭</span>
        <span class="login-playful-icon login-playful-icon--rainbow" title="">🌈</span>
        <span class="login-playful-icon login-playful-icon--flower" title="">🌸</span>
    </div>

    <div class="login-page-layout">
        <div class="login-left">
            <div class="login-card">
                <div class="logo-section">
                    <img src="logo.png" alt="Logo Sekolah" class="school-logo">
                </div>

                <h1 class="login-title">
                    Sistem Penerimaan Murid Baru (SPMB)<br>
                    TA. 2026/2027
                </h1>

                <p class="school-name">
                    UPTD SD NEGERI NO. 071184 TETESUA
                </p>

                <?php if ($error !== ''): ?>
                    <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form class="login-form" action="login_proses.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>

            <div class="login-credit-bar">
                <?php
                $app_credit_variant = 'login';
                require __DIR__ . '/includes/app_credit.php';
                ?>
            </div>
        </div>

        <div class="login-posters-zone">
            <?php
            $sambutanConfig = require __DIR__ . '/includes/sambutan_config.php';
            $loginSlideshow = !empty($sambutanConfig['aktif']);
            ?>

            <?php if ($loginSlideshow): ?>
            <div class="login-slideshow" id="loginSlideshow" aria-live="polite">
                <div class="login-slideshow-nav" role="tablist" aria-label="Informasi halaman login">
                    <button type="button" class="login-slideshow-tab is-active" role="tab" data-slide-index="0" aria-selected="true">
                        <i class="bi bi-info-circle"></i>
                        <span>Info Pendaftaran</span>
                    </button>
                    <button type="button" class="login-slideshow-tab" role="tab" data-slide-index="1" aria-selected="false">
                        <i class="bi bi-chat-heart"></i>
                        <span>Sambutan Kasek</span>
                    </button>
                </div>

                <div class="login-slideshow-viewport">
                    <div class="login-slide login-slide-info is-active" data-slide-id="info" aria-hidden="false">
                        <div class="login-posters-row">
                            <?php require __DIR__ . '/includes/login_poster_kiri.php'; ?>
                            <?php require __DIR__ . '/includes/login_poster_kanan.php'; ?>
                        </div>
                    </div>
                    <?php require __DIR__ . '/includes/login_slide_sambutan.php'; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="login-posters-row">
                <?php require __DIR__ . '/includes/login_poster_kiri.php'; ?>
                <?php require __DIR__ . '/includes/login_poster_kanan.php'; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($loginSlideshow)): ?>
    <script src="assets/js/login-slideshow.js?v=2"></script>
    <?php endif; ?>
</body>
</html>
