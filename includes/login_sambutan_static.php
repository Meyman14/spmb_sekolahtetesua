<?php
$sambutan = require __DIR__ . '/sambutan_config.php';

if (empty($sambutan['aktif'])) {
    return;
}
?>
<article class="login-sambutan-card poster-card poster-scroll">
    <header class="login-sambutan-hero">
        <span class="poster-badge">SAMBUTAN RESMI</span>
        <h2 class="login-sambutan-title"><?php echo htmlspecialchars($sambutan['judul']); ?></h2>
        <p class="login-sambutan-year"><?php echo htmlspecialchars($sambutan['tahun_ajaran']); ?></p>
        <p class="login-sambutan-salam"><?php echo htmlspecialchars($sambutan['salam']); ?></p>
    </header>

    <div class="login-sambutan-body">
        <blockquote class="login-sambutan-quote">
            <i class="bi bi-quote"></i>
            <?php echo htmlspecialchars($sambutan['kutipan']); ?>
        </blockquote>

        <?php foreach ($sambutan['paragraf'] as $teks): ?>
            <p class="login-sambutan-p"><?php echo htmlspecialchars($teks); ?></p>
        <?php endforeach; ?>

        <h3 class="login-sambutan-subtitle"><?php echo htmlspecialchars($sambutan['judul_alasan']); ?></h3>

        <ul class="login-sambutan-alasan">
            <?php foreach ($sambutan['alasan'] as $item): ?>
                <li class="login-sambutan-alasan-item">
                    <span class="login-sambutan-alasan-icon">
                        <i class="bi bi-<?php echo htmlspecialchars($item['icon']); ?>"></i>
                    </span>
                    <div>
                        <strong><?php echo htmlspecialchars($item['judul']); ?></strong>
                        <p><?php echo htmlspecialchars($item['teks']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php foreach ($sambutan['paragraf_penutup'] as $teks): ?>
            <p class="login-sambutan-p"><?php echo htmlspecialchars($teks); ?></p>
        <?php endforeach; ?>

        <footer class="login-sambutan-sign">
            <?php if (!empty($sambutan['foto'])): ?>
                <div class="login-sambutan-foto">
                    <img src="<?php echo htmlspecialchars($sambutan['foto']); ?>" alt="Foto Kepala Sekolah">
                </div>
            <?php else: ?>
                <div class="login-sambutan-foto login-sambutan-foto-ph" aria-hidden="true">
                    <i class="bi bi-person-fill"></i>
                </div>
            <?php endif; ?>
            <div>
                <p class="login-sambutan-salam-akhir"><?php echo htmlspecialchars($sambutan['salam_penutup']); ?></p>
                <p class="login-sambutan-nama"><?php echo htmlspecialchars($sambutan['nama']); ?></p>
                <p class="login-sambutan-jabatan"><?php echo htmlspecialchars($sambutan['jabatan']); ?></p>
            </div>
        </footer>
    </div>

    <div class="poster-scroll-hint">
        <i class="bi bi-chevron-double-down"></i> Gulir untuk membaca
    </div>
</article>
