<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/ensure_siswa_schema.php';
require_once 'includes/stats.php';
require_once 'includes/petugas_jadwal.php';
require_once 'includes/ensure_absensi_schema.php';
require_once 'includes/absensi.php';

ensureSiswaSchema($conn);
ensureAbsensiSchema($conn);

$page_title = 'Dashboard';
$active_menu = 'dashboard';
$extra_css = 'assets/css/dashboard-home.css';

$accessError = $_SESSION['access_error'] ?? '';
unset($_SESSION['access_error']);

$stat = getStatistikTerdaftar($conn);
$infoJadwal = infoPetugasHariIni();
$petugasHadirHariIni = daftarPetugasHadirTanggal($conn, tanggalAbsensiHariIni());
$namaPengguna = $_SESSION['nama'] ?? $_SESSION['username'];
$username = $_SESSION['username'] ?? '';

// Tanggal yang dipakai untuk menampilkan jadwal petugas (bisa dioverride via ?petugas_date=YYYY-MM-DD)
$petugasDisplayDate = $_GET['petugas_date'] ?? tanggalAbsensiHariIni();

// Cek apakah user (petugas) sudah hadir hari ini
$sudahHadirHariIni = false;
$waktuAbsenPetugas = null;
if (isPetugas()) {
    $sudahHadirHariIni = petugasSudahHadirHariIni($conn, $username);
    if ($sudahHadirHariIni) {
        // Ambil waktu absen petugas
        $tanggalHariIni = tanggalAbsensiHariIni();
        $stmt = mysqli_prepare($conn, "SELECT waktu_masuk FROM daftar_hadir WHERE username = ? AND tanggal = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $tanggalHariIni);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        $waktuAbsenPetugas = $row['waktu_masuk'] ?? null;
    }
}

require_once 'includes/head.php';
require_once 'includes/sidebar.php';
?>

<main class="main-content dashboard-page">
    <header class="dash-welcome">
        <div class="dash-welcome-left">
            <img src="logo.png" alt="Logo Nias Barat" class="dash-logo">
            <div>
                <p class="dash-welcome-label">Selamat datang</p>
                <h1 class="dash-welcome-name"><?php echo htmlspecialchars($namaPengguna); ?></h1>
                <p class="dash-welcome-school">UPTD SD Negeri No. 071184 Tetesua · SPMB TA. 2026/2027</p>
            </div>
        </div>
        <div class="dash-welcome-time">
            <span class="dash-time-item"><i class="bi bi-calendar3"></i> <span id="dashHari"><?php echo htmlspecialchars($infoJadwal['hari']); ?></span></span>
            <span class="dash-time-item"><i class="bi bi-clock"></i> <span id="dashTanggal"><?php echo htmlspecialchars($infoJadwal['tanggal']); ?></span></span>
            <span class="dash-time-item dash-time-live"><i class="bi bi-alarm"></i> <span id="jamDashboard"><?php echo date('H:i:s'); ?></span></span>
        </div>
    </header>

    <?php if ($accessError !== ''): ?>
    <div class="alert alert-warning alert-dismissible fade show dashboard-alert">
        <?php echo htmlspecialchars($accessError); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isPetugas() && $sudahHadirHariIni): ?>
    <div class="alert alert-success alert-dismissible fade show dashboard-alert">
        <i class="bi bi-check-circle-fill"></i> <strong>Status Absensi:</strong> Anda sudah tercatat hadir pada pukul <?php echo date('H:i:s', strtotime($waktuAbsenPetugas)); ?>.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3 dash-main-row">
        <div class="col-lg-8">
            <p class="dash-stats-caption">Data murid <strong>terdaftar</strong> (sudah diverifikasi admin)</p>
            <div class="row g-3">
                <div class="col-sm-6 col-xl-4">
                    <div class="dash-stat-box dash-stat-total">
                        <span class="dash-stat-label">Total Pendaftar</span>
                        <span class="dash-stat-value"><?php echo $stat['total']; ?></span>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="dash-stat-box dash-stat-laki">
                        <span class="dash-stat-label">Laki-laki</span>
                        <span class="dash-stat-value"><?php echo $stat['laki']; ?></span>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="dash-stat-box dash-stat-perempuan">
                        <span class="dash-stat-label">Perempuan</span>
                        <span class="dash-stat-value"><?php echo $stat['perempuan']; ?></span>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="dash-stat-box dash-stat-kristen">
                        <span class="dash-stat-label">Kristen</span>
                        <span class="dash-stat-value"><?php echo $stat['kristen']; ?></span>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="dash-stat-box dash-stat-katolik">
                        <span class="dash-stat-label">Katolik</span>
                        <span class="dash-stat-value"><?php echo $stat['katolik']; ?></span>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="dash-stat-box dash-stat-islam">
                        <span class="dash-stat-label">Islam</span>
                        <span class="dash-stat-value"><?php echo $stat['islam']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <aside class="dash-info-panel">
                <h2 class="dash-info-title"><i class="bi bi-people-fill"></i> Informasi</h2>
                <p class="dash-info-sub">Petugas yang sudah absen hadir hari ini</p>
                <div class="dash-info-datetime">
                    <div><strong id="infoPanelHari"><?php echo htmlspecialchars($infoJadwal['hari']); ?></strong></div>
                    <div id="infoPanelTanggal"><?php echo htmlspecialchars($infoJadwal['tanggal']); ?></div>
                    <div class="text-muted small" id="infoPanelJam"><?php echo htmlspecialchars($infoJadwal['jam']); ?></div>
                </div>
                <div class="dash-scheduled" id="dashScheduled">
                    <h3 class="dash-scheduled-title">Petugas Hari Ini (Jadwal) — <span id="dashScheduledDate"></span></h3>
                    <ul class="dash-scheduled-list" id="dashScheduledList">
                        <li class="loading">Memuat...</li>
                    </ul>
                </div>
                <?php if (!empty($petugasHadirHariIni)): ?>
                <ul class="dash-petugas-list" id="dashPetugasList">
                    <?php foreach ($petugasHadirHariIni as $row): ?>
                    <li>
                        <?php echo htmlspecialchars($row['nama_petugas']); ?>
                        <span class="small text-muted"> · <?php echo date('H:i', strtotime($row['waktu_masuk'])); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="dash-petugas-kosong">Belum ada petugas yang absen hadir hari ini.</p>
                <?php endif; ?>
                <p class="dash-info-note small text-muted mb-0">
                    Daftar diperbarui otomatis setelah petugas konfirmasi absensi.
                </p>
            </aside>
        </div>
    </div>
</main>

<script src="data/petugas_hari_ini.js"></script>
<script>
// Populate scheduled petugas using server date
(function(){
    try {
        var serverDate = '<?php echo tanggalAbsensiHariIni(); ?>';
        var displayDate = '<?php echo $petugasDisplayDate; ?>';
        var list = window.PetugasHariIni ? PetugasHariIni.getByDate(displayDate) : [];
        var container = document.getElementById('dashScheduledList');
        if (!container) return;
        container.innerHTML = '';
        // Jika tidak ada entry untuk tanggal yang dipilih, cari ke depan sampai 14 hari
        var shownDate = displayDate;
        if (!list || list.length === 0) {
            var found = false;
            var start = new Date(displayDate);
            for (var i = 1; i <= 30; i++) {
                var d = new Date(start);
                d.setDate(start.getDate() + i);
                var y = d.getFullYear();
                var m = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                var key = y + '-' + m + '-' + day;
                var nextList = window.PetugasHariIni ? PetugasHariIni.getByDate(key) : [];
                if (nextList && nextList.length > 0) {
                    list = nextList;
                    shownDate = key;
                    found = true;
                    break;
                }
            }
        }

        // Tampilkan tanggal yang digunakan
        var dashDateEl = document.getElementById('dashScheduledDate');
        if (dashDateEl) {
            // convert YYYY-MM-DD -> DD MMMM YYYY (Indonesian month)
            function isoToIndo(s) {
                if (!s) return '';
                var m = s.split('-');
                if (m.length !== 3) return s;
                var day = m[2];
                var month = parseInt(m[1], 10);
                var year = m[0];
                var bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                return ('0' + day).slice(-2) + ' ' + (bulan[month-1] || '') + ' ' + year;
            }
            dashDateEl.textContent = isoToIndo(shownDate);
        }

        if (!list || list.length === 0) {
            var li = document.createElement('li');
            li.className = 'dash-scheduled-empty';
            li.textContent = 'Tidak ada petugas terjadwal.';
            container.appendChild(li);
            return;
        }

        list.forEach(function(name){
            var li = document.createElement('li');
            li.className = 'dash-scheduled-item';
            li.textContent = name;
            container.appendChild(li);
        });
    } catch (e) {
        console.error('Gagal memuat petugas terjadwal:', e);
    }
})();
</script>

<script>
(function () {
    // Variabel untuk menyimpan waktu server
    var serverTime = null;
    var lastServerSync = 0;
    var syncInterval = 5000; // Sinkronisasi ulang setiap 5 detik untuk akurasi
    
    // Function untuk fetch waktu dari server database
    function fetchServerTime() {
        fetch('api_get_time.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    serverTime = {
                        hari: data.hari,
                        tanggal: data.tanggal,
                        jam_server: data.jam,  // Format: HH:MM:SS
                        unix_timestamp: data.unix_timestamp * 1000 // milliseconds
                    };
                    lastServerSync = Date.now();
                    updateDashboardTime();
                }
            })
            .catch(error => {
                console.error('Gagal mensinkronisasi waktu server:', error);
            });
    }
    
    // Function untuk update display dengan waktu server
    function updateDashboardTime() {
        if (!serverTime) {
            return;
        }
        
        // Parse jam server (HH:MM:SS)
        var jam_parts = serverTime.jam_server.split(':');
        var jam_base = parseInt(jam_parts[0]);
        var menit_base = parseInt(jam_parts[1]);
        var detik_base = parseInt(jam_parts[2]);
        
        // Hitung elapsed time sejak last sync
        var elapsedMs = Date.now() - lastServerSync;
        var elapsedSec = Math.floor(elapsedMs / 1000);
        
        // Update waktu berdasarkan elapsed seconds
        var totalDetik = (jam_base * 3600) + (menit_base * 60) + detik_base + elapsedSec;
        
        // Handle day wrap-around (dalam 1 hari = 86400 detik)
        totalDetik = totalDetik % 86400;
        
        var jam = Math.floor(totalDetik / 3600);
        var menit = Math.floor((totalDetik % 3600) / 60);
        var detik = totalDetik % 60;
        
        // Format dengan leading zero
        var jam_str = String(jam).padStart(2, '0');
        var menit_str = String(menit).padStart(2, '0');
        var detik_str = String(detik).padStart(2, '0');
        var jam_display = jam_str + ':' + menit_str + ':' + detik_str;
        
        // Update header welcome time
        var elemen_hari = document.getElementById('dashHari');
        var elemen_tanggal = document.getElementById('dashTanggal');
        var elemen_jam = document.getElementById('jamDashboard');
        
        if (elemen_hari) elemen_hari.textContent = serverTime.hari;
        if (elemen_tanggal) elemen_tanggal.textContent = serverTime.tanggal;
        if (elemen_jam) elemen_jam.textContent = jam_display;
        
        // Update info panel time
        var infoHari = document.getElementById('infoPanelHari');
        var infoTanggal = document.getElementById('infoPanelTanggal');
        var infoJam = document.getElementById('infoPanelJam');
        
        if (infoHari) infoHari.textContent = serverTime.hari;
        if (infoTanggal) infoTanggal.textContent = serverTime.tanggal;
        if (infoJam) infoJam.textContent = jam_display + ' WIB';
    }
    
    // Initialize - fetch pertama kali
    fetchServerTime();
    
    // Update display setiap 1 detik
    var displayInterval = setInterval(updateDashboardTime, 1000);
    
    // Sinkronisasi ulang dengan server setiap 5 detik
    var syncTimer = setInterval(fetchServerTime, syncInterval);
})();
</script>

<?php require_once 'includes/footer.php'; ?>
