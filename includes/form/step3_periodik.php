<?php $d = $formData; ?>
<form method="post" action="input_murid_proses.php" enctype="multipart/form-data" class="row g-3" id="formStep3">
    <?php if (formEditId() !== null): ?>
    <input type="hidden" name="edit_id" value="<?php echo (int) formEditId(); ?>">
    <?php endif; ?>

    <div class="col-12"><h6 class="form-section-title">Data Periodik</h6></div>
    <div class="col-md-4">
        <label class="form-label">Tinggi Badan (cm) <span class="text-danger final-only">*</span></label>
        <input type="number" name="tinggi_badan" class="form-control" step="0.1" min="50" max="250" value="<?php echo formOld($d, 'tinggi_badan'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Berat Badan (kg) <span class="text-danger final-only">*</span></label>
        <input type="number" name="berat_badan" class="form-control" step="0.1" min="5" max="200" value="<?php echo formOld($d, 'berat_badan'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Lingkar Kepala (cm)</label>
        <input type="number" name="lingkar_kepala" class="form-control" step="0.1" min="20" max="80" value="<?php echo formOld($d, 'lingkar_kepala'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Jarak ke Sekolah (km)</label>
        <input type="number" name="jarak_sekolah" class="form-control" step="0.1" min="0" value="<?php echo formOld($d, 'jarak_sekolah'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Waktu Tempuh</label>
        <input type="text" name="waktu_tempuh" class="form-control" placeholder="Contoh: 15 menit" value="<?php echo formOld($d, 'waktu_tempuh'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Jumlah Saudara Kandung</label>
        <input type="number" name="jumlah_saudara" class="form-control" min="0" max="20" value="<?php echo formOld($d, 'jumlah_saudara'); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">Hobi</label>
        <input type="text" name="hobi" class="form-control" placeholder="Contoh: Menggambar, bermain bola"
               value="<?php echo formOld($d, 'hobi'); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Cita-cita</label>
        <input type="text" name="cita_cita" class="form-control" placeholder="Contoh: Guru, Dokter"
               value="<?php echo formOld($d, 'cita_cita'); ?>">
    </div>

    <div class="col-12"><h6 class="form-section-title">Upload Berkas</h6></div>
    <p class="upload-hint col-12 mb-2">
        Format: JPG, PNG, atau PDF. Maksimal 2 MB per berkas.
        <?php if (formEditId() !== null): ?>
        Kosongkan upload jika berkas sudah ada dan tidak diganti.
        <?php endif; ?>
        <strong>Simpan Final</strong> mewajibkan semua berkas tersedia.
    </p>

    <?php
    $uploads = [
        'file_kk' => 'Foto KK',
        'file_akte' => 'Akte Kelahiran',
        'file_ijazah' => 'Ijazah TK',
        'file_kks' => 'KKS/PKH',
        'file_foto' => 'Foto Siswa',
        'file_surat_orangtua' => 'Surat Pernyataan Orang Tua',
    ];
    foreach ($uploads as $name => $label):
        $existingKey = $name . '_existing';
        $existingFile = $d[$existingKey] ?? '';
    ?>
    <div class="col-md-6">
        <label class="form-label"><?php echo $label; ?></label>
        <?php if ($existingFile !== ''): ?>
        <div class="small mb-1">
            <span class="badge bg-success">Sudah ada</span>
            <a href="<?php echo htmlspecialchars($existingFile); ?>" target="_blank" class="ms-1">Lihat berkas</a>
        </div>
        <?php endif; ?>
        <input type="file" name="<?php echo $name; ?>" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
    </div>
    <?php endforeach; ?>

    <div class="col-12">
        <div class="alert alert-light border small mb-0">
            <strong>Simpan Draft:</strong> data belum lengkap dapat dilengkapi/diedit nanti (menu Status → Draft).<br>
            <strong>Simpan Final:</strong> data sudah lengkap, masuk ke menu Status → Final untuk verifikasi admin.
        </div>
    </div>

    <div class="form-actions form-actions-step3">
        <div class="form-actions-left">
            <a href="<?php echo formMuridUrl(2); ?>" class="btn btn-outline-secondary btn-sebelumnya">
                <i class="bi bi-arrow-left"></i> Sebelumnya
            </a>
        </div>
        <div class="form-actions-right">
            <button type="submit" name="action" value="submit_draft" class="btn btn-warning btn-simpan-draft" formnovalidate>
                <i class="bi bi-save"></i> Simpan Draft
            </button>
            <button type="submit" name="action" value="submit_final" class="btn btn-success btn-simpan-final">
                <i class="bi bi-check-circle"></i> Simpan Final
            </button>
        </div>
    </div>
</form>
