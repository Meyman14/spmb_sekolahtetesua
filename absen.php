<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/absensi.php';
require_once 'includes/face_recognition.php';

requirePetugasOrAdmin();
ensureAbsensiSchema($conn);

$page_title = 'Absensi Petugas';
$active_menu = 'absen';
$extra_css = 'assets/css/absen.css';

$namaPengguna = $_SESSION['nama'] ?? $_SESSION['username'];
$username = $_SESSION['username'] ?? '';
$sudahHadir = isPetugas() ? petugasSudahHadirHariIni($conn, $username) : false;
// Feature: reference-face removed — always allow camera capture for attendance
// $punyaWajahRef and reference data removed

$hariIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabat'];
$bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tanggalTeks = $hariIndo[(int) date('w')] . ', ' . date('j') . ' ' . $bulanIndo[(int) date('n') - 1] . ' ' . date('Y');

$pesan = $_SESSION['absen_pesan'] ?? '';
$pesanTipe = $_SESSION['absen_pesan_tipe'] ?? 'success';
unset($_SESSION['absen_pesan'], $_SESSION['absen_pesan_tipe']);

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content app-page absen-page">
    <div class="absen-card absen-card-wide mx-auto">
        <div class="absen-card-header text-center">
            <div class="absen-icon-wrap">
                <i class="bi bi-person-check"></i>
            </div>
            <h1 class="h4 fw-bold mb-1">Absensi Petugas</h1>
            <p class="small mb-0 opacity-90">Lakukan absensi untuk mencatat kehadiran ke menu Daftar Hadir admin.</p>
        </div>

        <div class="absen-card-body">
            <!-- Pengecekan jika sudah pernah absen sebelumnya -->
            <?php if ($pesan !== '' && strpos($pesan, 'telah melakukan absensi') !== false): ?>
            <div class="alert alert-warning">
                <?php echo htmlspecialchars($pesan); ?>
            </div>
            <div class="text-center mt-4">
                <div class="mb-4">
                    <i class="bi bi-info-circle" style="font-size: 3rem; color: #ffc107;"></i>
                </div>
                <p class="text-muted mb-4" style="font-size: 1.1rem;">
                    <strong>Absensi Anda Sudah Tercatat</strong>
                </p>
                <a href="dashboard.php" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-arrow-right me-2"></i>Lanjutkan Bekerja
                </a>
            </div>

            <!-- Jika berhasil absen sekarang -->
            <?php elseif ($pesan !== '' && $pesanTipe === 'success'): ?>
            <div class="text-center">
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($pesan); ?>
                </div>
                <a href="dashboard.php" class="btn btn-primary btn-lg w-100">Lanjut ke Dashboard</a>
            </div>

            <!-- Jika belum absen, tampilkan form absensi -->
            <?php else: ?>

            <div class="absen-info-box mb-3">
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <span class="absen-info-label">Petugas</span>
                        <strong class="d-block small"><?php echo htmlspecialchars($namaPengguna); ?></strong>
                    </div>
                    <div class="col-4">
                        <span class="absen-info-label">Hari</span>
                        <strong class="d-block"><?php echo htmlspecialchars($hariIndo[(int) date('w')]); ?></strong>
                    </div>
                    <div class="col-4">
                        <span class="absen-info-label">Jam</span>
                        <strong class="d-block" id="jamAbsen"><?php echo date('H:i:s'); ?></strong>
                    </div>
                </div>
                <p class="text-center mb-0 mt-2 small text-secondary"><?php echo htmlspecialchars($tanggalTeks); ?></p>
            </div>

            <div class="absen-camera-wrap mb-3">
                <video id="absenVideo" class="absen-video" autoplay muted playsinline></video>
                <canvas id="absenCanvas" class="d-none"></canvas>
            </div>

            <div id="absenFaceStatus" class="absen-face-status alert alert-info">
                Menyiapkan kamera...
            </div>

            <button type="button" id="btnVerifikasiWajah" class="btn btn-success btn-lg w-100">
                <i class="bi bi-camera2 me-2"></i>Ambil Foto &amp; Absen Hadir
            </button>
            <p class="small text-muted text-center mt-2 mb-0">
                Sistem akan menyimpan foto tangkapan hari ini sebagai bukti kehadiran.
            </p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="assets/js/absen-face.js?v=2"></script>
<script>
(function () {
    var el = document.getElementById('jamAbsen');
    if (!el) return;
    setInterval(function () {
        el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }, 1000);
})();
</script>
<!-- Modal sukses absen -->
<style>
/* Minimal, bright modal style for attendance success */
.custom-absen-modal .modal-dialog { max-width: 720px; }
.custom-absen-modal .modal-content {
    background: linear-gradient(180deg, #e8f4ff 0%, #dff0ff 100%);
    border-radius: 22px;
    border: 1px solid rgba(0,0,0,0.07);
    box-shadow: 0 12px 30px rgba(13,40,80,0.12);
    overflow: hidden;
}
.custom-absen-modal .modal-body { padding: 36px 40px; }
.custom-absen-modal h5 { margin: 0 0 10px; font-weight:800; color:#073b66; font-size:1.25rem; letter-spacing:0.6px; }
.custom-absen-modal p.lead { margin:0; color:#0b4f78; font-weight:600; }
.custom-absen-modal .panel-author { margin-top:12px; color:#083a5a; font-weight:700; }
.custom-absen-modal .btn-absen-continue {
    display:inline-block; margin-top:18px; padding:12px 22px; background:linear-gradient(180deg,#0d6efd,#0a58ca); color:#fff; border-radius:8px; border:none; font-weight:800; box-shadow: 0 8px 18px rgba(13,110,253,0.18);
}
.modal-backdrop.custom-backdrop { background: rgba(6,25,46,0.45); z-index: 1040; }
.custom-absen-modal { z-index: 1100; }
</style>

<div class="modal fade custom-absen-modal" id="modalAbsenSukses" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5>TERIMAKASIH TELAH HADIR</h5>
                <p class="lead">Semangat untuk melaksanakan tugas — semoga murid baru bertambah</p>
                <div class="panel-author">@Panitia</div>
                <div>
                    <button id="btnLanjutKerja" class="btn-absen-continue">Silahkan Lanjut</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
        var pesan = <?php echo json_encode($pesan); ?>;
        var tipe = <?php echo json_encode($pesanTipe); ?>;
        if (pesan && tipe === 'success') {
                var modalEl = document.getElementById('modalAbsenSukses');
                if (modalEl) {
                        // show modal: if Bootstrap available, use it; otherwise manual
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                var bsModal = new bootstrap.Modal(modalEl);
                                bsModal.show();
                        } else {
                                var backdrop = document.createElement('div');
                                backdrop.className = 'modal-backdrop fade show custom-backdrop';
                                document.body.appendChild(backdrop);
                                modalEl.style.display = 'block';
                                modalEl.classList.add('show');
                                document.body.classList.add('modal-open');
                        }
                        var btn = document.getElementById('btnLanjutKerja');
                        if (btn) btn.addEventListener('click', function () { window.location.href = 'dashboard.php'; });
                }
        }
});
</script>
<?php require_once 'includes/footer.php'; ?>
