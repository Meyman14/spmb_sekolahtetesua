<?php
$nama_pengguna = $_SESSION['nama'] ?? $_SESSION['username'];
$status_submenu_open = in_array($active_menu, ['draft', 'final', 'verifikasi'], true);

if (!function_exists('petugasBolehAksesMenu')) {
    require_once __DIR__ . '/absensi.php';
}
$tampilkanMenuUtama = petugasBolehAksesMenu();
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo-wrap">
            <img src="logo.png" alt="Logo" class="sidebar-logo" onerror="this.style.display='none'">
        </div>
        <div class="sidebar-brand-text">
            <span class="brand-title">SPMB</span>
            <span class="brand-sub">TA. 2026/2027</span>
            <span class="brand-school">SD Negeri 071184 Tetesua</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php if (!$tampilkanMenuUtama && isPetugas()): ?>
        <div class="sidebar-absen-notice">
            <i class="bi bi-exclamation-circle me-1"></i>
            Menu terkunci. Silakan <strong>absen hadir</strong> terlebih dahulu.
        </div>
        <a href="absen.php" class="nav-link <?php echo ($active_menu ?? '') === 'absen' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-calendar-check"></i></span>
            <span class="nav-link-text">Absensi Hari Ini</span>
        </a>
        <?php else: ?>
        <p class="sidebar-nav-label"><i class="bi bi-grid-3x3-gap-fill"></i> Menu Utama</p>

        <a href="dashboard.php" class="nav-link <?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-speedometer2"></i></span>
            <span class="nav-link-text">Dashboard</span>
        </a>

        <?php if (isAdmin() || isPetugas()): ?>
        <a href="absen.php" class="nav-link <?php echo $active_menu === 'absen' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-person-check"></i></span>
            <span class="nav-link-text">Absensi Petugas</span>
        </a>
        <?php endif; ?>

        <a href="terdaftar.php" class="nav-link <?php echo $active_menu === 'terdaftar' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-people"></i></span>
            <span class="nav-link-text">Terdaftar</span>
        </a>

        <?php if (isPetugas()): ?>
        <a href="input_murid.php" class="nav-link <?php echo $active_menu === 'input' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-person-plus"></i></span>
            <span class="nav-link-text">Input Murid Baru</span>
        </a>
        <?php endif; ?>

        <?php if (isAdmin()): ?>
        <a href="status_final.php" class="nav-link <?php echo $active_menu === 'verifikasi' || $active_menu === 'final' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-shield-check"></i></span>
            <span class="nav-link-text">Verifikasi Dokumen</span>
        </a>

        <?php
        $daftar_sub = $_GET['sub'] ?? '';
        $daftar_open = $active_menu === 'daftar_hadir' ? true : false;
        ?>
        <div class="nav-dropdown">
            <button class="nav-link nav-dropdown-toggle <?php echo $daftar_open ? 'active' : ''; ?>"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#menuDaftarHadir"
                    aria-expanded="<?php echo $daftar_open ? 'true' : 'false'; ?>">
                <span class="nav-link-icon"><i class="bi bi-calendar-check"></i></span>
                <span class="nav-link-text">Daftar Hadir Petugas</span>
                <i class="bi bi-chevron-down ms-auto dropdown-icon"></i>
            </button>
            <div class="collapse nav-submenu <?php echo $daftar_open ? 'show' : ''; ?>" id="menuDaftarHadir">
                <a href="daftar_hadir.php?sub=kehadiran" class="nav-sublink <?php echo $daftar_sub === 'kehadiran' ? 'active' : ''; ?>">
                    <i class="bi bi-list-check"></i> Kehadiran
                </a>
                <a href="foto_petugas.php" class="nav-sublink <?php echo $daftar_sub === 'foto_petugas' ? 'active' : ''; ?>">
                    <i class="bi bi-camera-fill"></i> Foto Petugas
                </a>
                <a href="admin_clear_absensi.php" class="nav-sublink text-danger <?php echo basename($_SERVER['SCRIPT_NAME']) === 'admin_clear_absensi.php' ? 'active' : ''; ?>">
                    <i class="bi bi-trash"></i> Reset Kehadiran
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isPetugas()): ?>
        <div class="nav-dropdown">
            <button class="nav-link nav-dropdown-toggle <?php echo $status_submenu_open ? 'active' : ''; ?>"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#menuStatus"
                    aria-expanded="<?php echo $status_submenu_open ? 'true' : 'false'; ?>">
                <span class="nav-link-icon"><i class="bi bi-folder2-open"></i></span>
                <span class="nav-link-text">Status</span>
                <i class="bi bi-chevron-down ms-auto dropdown-icon"></i>
            </button>
            <div class="collapse nav-submenu <?php echo $status_submenu_open ? 'show' : ''; ?>" id="menuStatus">
                <a href="status_draft.php" class="nav-sublink <?php echo $active_menu === 'draft' ? 'active' : ''; ?>">
                    <i class="bi bi-pencil-square"></i> Draft
                </a>
                <a href="status_final.php" class="nav-sublink <?php echo $active_menu === 'final' ? 'active' : ''; ?>">
                    <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                </a>
            </div>
        </div>
        <?php else: ?>
        <a href="status_draft.php" class="nav-link <?php echo $active_menu === 'draft' ? 'active' : ''; ?>">
            <span class="nav-link-icon"><i class="bi bi-pencil-square"></i></span>
            <span class="nav-link-text">Data Draft</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user-card">
            <div class="user-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="user-meta">
                <span class="user-name"><?php echo htmlspecialchars($nama_pengguna); ?></span>
                <span class="user-role"><?php echo roleLabel(); ?></span>
            </div>
        </div>
        <a href="logout.php" class="btn btn-sidebar-logout w-100">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </div>
</aside>
<div class="app-body">
