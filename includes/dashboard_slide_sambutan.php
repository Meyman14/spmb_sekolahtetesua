<?php
$sambutan = require __DIR__ . '/sambutan_config.php';

if (empty($sambutan['aktif'])) {
    return;
}
?>
<div class="dashboard-slide dashboard-slide-sambutan" data-slide-id="sambutan" aria-hidden="true">
    <article class="sambutan-card">
        <header class="sambutan-hero">
            <div class="sambutan-hero-deco" aria-hidden="true">
                <i class="bi bi-stars"></i>
            </div>
            <span class="sambutan-hero-badge">SAMBUTAN RESMI</span>
            <h2 class="sambutan-hero-title"><?php echo htmlspecialchars($sambutan['judul']); ?></h2>
            <p class="sambutan-hero-year">Tahun Ajaran <?php echo htmlspecialchars($sambutan['tahun_ajaran']); ?></p>
            <p class="sambutan-hero-salam"><?php echo htmlspecialchars($sambutan['salam']); ?></p>
        </header>

        <div class="sambutan-scroll">
            <blockquote class="sambutan-quote">
                <i class="bi bi-quote"></i>
                <?php echo htmlspecialchars($sambutan['kutipan']); ?>
            </blockquote>

            <?php foreach ($sambutan['paragraf'] as $teks): ?>
            <p class="sambutan-paragraf"><?php echo htmlspecialchars($teks); ?></p>
            <?php endforeach; ?>

            <h3 class="sambutan-section-title"><?php echo htmlspecialchars($sambutan['judul_alasan']); ?></h3>

            <div class="sambutan-alasan-grid">
                <?php foreach ($sambutan['alasan'] as $item): ?>
                <div class="sambutan-alasan-item">
                    <div class="sambutan-alasan-icon">
                        <i class="bi bi-<?php echo htmlspecialchars($item['icon']); ?>"></i>
                    </div>
                    <strong><?php echo htmlspecialchars($item['judul']); ?></strong>
                    <p><?php echo htmlspecialchars($item['teks']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <?php foreach ($sambutan['paragraf_penutup'] as $teks): ?>
            <p class="sambutan-paragraf"><?php echo htmlspecialchars($teks); ?></p>
            <?php endforeach; ?>
        </div>

        <footer class="sambutan-footer">
            <div class="sambutan-footer-inner">
                <?php if (!empty($sambutan['foto'])): ?>
                <div class="sambutan-foto">
                    <img src="<?php echo htmlspecialchars($sambutan['foto']); ?>" alt="Foto Kepala Sekolah">
                </div>
                <?php else: ?>
                <div class="sambutan-foto sambutan-foto-placeholder" aria-hidden="true">
                    <i class="bi bi-person-fill"></i>
                </div>
                <?php endif; ?>

                <div class="sambutan-tanda-tangan">
                    <p class="sambutan-salam-akhir"><?php echo htmlspecialchars($sambutan['salam_penutup']); ?></p>
                    <p class="sambutan-nama"><?php echo htmlspecialchars($sambutan['nama']); ?></p>
                    <p class="sambutan-jabatan"><?php echo htmlspecialchars($sambutan['jabatan']); ?></p>
                    <p class="sambutan-sekolah"><?php echo htmlspecialchars($sambutan['sekolah']); ?></p>
                </div>
            </div>
        </footer>
    </article>
</div>
