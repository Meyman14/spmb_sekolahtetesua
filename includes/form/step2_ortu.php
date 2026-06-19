<?php $d = $formData; ?>
<form method="post" action="input_murid_proses.php" class="row g-3" id="formStep2">
    <?php if (formEditId() !== null): ?>
    <input type="hidden" name="edit_id" value="<?php echo (int) formEditId(); ?>">
    <?php endif; ?>

    <div class="col-12"><h6 class="form-section-title">Data Ayah</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nama Ayah <span class="text-danger">*</span></label>
        <input type="text" name="nama_ayah" class="form-control input-uppercase" required
               autocomplete="off" value="<?php echo formOld($d, 'nama_ayah'); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">NIK Ayah</label>
        <input type="text" name="nik_ayah" class="form-control" maxlength="16" value="<?php echo formOld($d, 'nik_ayah'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Lahir Ayah</label>
        <input type="number" name="tahun_lahir_ayah" class="form-control" min="1940" max="2015" value="<?php echo formOld($d, 'tahun_lahir_ayah'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Pendidikan Ayah</label>
        <select name="pendidikan_ayah" class="form-select">
            <option value="">-- Pilih --</option>
            <?php foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'Diploma', 'S1', 'S2', 'S3'] as $pend): ?>
            <option value="<?php echo $pend; ?>" <?php echo formSelected($d, 'pendidikan_ayah', $pend); ?>><?php echo $pend; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Pekerjaan Ayah</label>
        <input type="text" name="pekerjaan_ayah" class="form-control" value="<?php echo formOld($d, 'pekerjaan_ayah'); ?>">
    </div>

    <div class="col-12"><h6 class="form-section-title">Data Ibu</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nama Ibu <span class="text-danger">*</span></label>
        <input type="text" name="nama_ibu" class="form-control input-uppercase" required
               autocomplete="off" value="<?php echo formOld($d, 'nama_ibu'); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">NIK Ibu</label>
        <input type="text" name="nik_ibu" class="form-control" maxlength="16" value="<?php echo formOld($d, 'nik_ibu'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Lahir Ibu</label>
        <input type="number" name="tahun_lahir_ibu" class="form-control" min="1940" max="2015" value="<?php echo formOld($d, 'tahun_lahir_ibu'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Pendidikan Ibu</label>
        <select name="pendidikan_ibu" class="form-select">
            <option value="">-- Pilih --</option>
            <?php foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'Diploma', 'S1', 'S2', 'S3'] as $pend): ?>
            <option value="<?php echo $pend; ?>" <?php echo formSelected($d, 'pendidikan_ibu', $pend); ?>><?php echo $pend; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Pekerjaan Ibu</label>
        <input type="text" name="pekerjaan_ibu" class="form-control" value="<?php echo formOld($d, 'pekerjaan_ibu'); ?>">
    </div>

    <div class="col-12"><h6 class="form-section-title">Data Wali (Opsional)</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nama Wali</label>
        <input type="text" name="nama_wali" class="form-control" value="<?php echo formOld($d, 'nama_wali'); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">NIK Wali</label>
        <input type="text" name="nik_wali" class="form-control" maxlength="16" value="<?php echo formOld($d, 'nik_wali'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Lahir Wali</label>
        <input type="number" name="tahun_lahir_wali" class="form-control" min="1940" max="2015" value="<?php echo formOld($d, 'tahun_lahir_wali'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Pendidikan Wali</label>
        <select name="pendidikan_wali" class="form-select">
            <option value="">-- Pilih --</option>
            <?php foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'Diploma', 'S1', 'S2', 'S3'] as $pend): ?>
            <option value="<?php echo $pend; ?>" <?php echo formSelected($d, 'pendidikan_wali', $pend); ?>><?php echo $pend; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Pekerjaan Wali</label>
        <input type="text" name="pekerjaan_wali" class="form-control" value="<?php echo formOld($d, 'pekerjaan_wali'); ?>">
    </div>

    <div class="col-12"><h6 class="form-section-title">Kontak</h6></div>
    <div class="col-md-6">
        <label class="form-label">Nomor HP</label>
        <input type="tel" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" value="<?php echo formOld($d, 'no_hp'); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo formOld($d, 'email'); ?>">
    </div>

    <div class="form-actions form-actions-step2">
        <div class="form-actions-left">
            <a href="<?php echo formMuridUrl(1); ?>" class="btn btn-outline-secondary btn-sebelumnya">
                <i class="bi bi-arrow-left"></i> Sebelumnya
            </a>
        </div>
        <div class="form-actions-right">
            <button type="submit" name="action" value="draft_step2" class="btn btn-warning btn-simpan-draft" formnovalidate>
                <i class="bi bi-save"></i> Simpan Draft
            </button>
            <button type="submit" name="action" value="save_step2" class="btn btn-primary btn-selanjutnya">
                Selanjutnya <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</form>
