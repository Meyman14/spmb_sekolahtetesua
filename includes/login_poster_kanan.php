<?php $jadwal = require __DIR__ . '/login_jadwal.php'; ?>
<aside class="login-poster login-poster-right" aria-label="Jadwal pendaftaran dan kegiatan sekolah">
    <div class="poster-card poster-scroll">
        <div class="poster-header poster-header-schedule">
            <span class="poster-badge">JADWAL</span>
            <h2 class="poster-title">Kegiatan Sekolah</h2>
            <p class="poster-sub">TA. <?php echo htmlspecialchars($jadwal['tahun_ajaran']); ?></p>
        </div>

        <ul class="schedule-list">
            <?php foreach ($jadwal['items'] as $item): ?>
            <li class="schedule-item<?php echo !empty($item['highlight']) ? ' schedule-item-highlight' : ''; ?>">
                <div class="schedule-icon">
                    <i class="bi bi-<?php echo htmlspecialchars($item['icon']); ?>"></i>
                </div>
                <div class="schedule-body">
                    <strong><?php echo htmlspecialchars($item['label']); ?></strong>
                    <span class="schedule-date"><?php echo htmlspecialchars($item['tanggal']); ?></span>
                    <p><?php echo htmlspecialchars($item['keterangan']); ?></p>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="poster-footnote">
            <i class="bi bi-pin-map"></i>
            UPTD SD Negeri No. 071184 Tetesua
        </div>
        <div class="poster-scroll-hint">
            <i class="bi bi-chevron-double-down"></i> Gulir untuk membaca
        </div>
    </div>
</aside>
