<?php $d = $formData; require_once __DIR__ . '/../helpers_date.php'; ?>
<form method="post" action="input_murid_proses.php" class="row g-3 formulir-step1" id="formStep1">
    <?php if (formEditId() !== null): ?>
    <input type="hidden" name="edit_id" value="<?php echo (int) formEditId(); ?>">
    <?php endif; ?>

    <div class="col-12">
        <?php require __DIR__ . '/step1_header.php'; ?>
    </div>

    <div class="col-12">
        <h6 class="form-section-title">Data Pribadi Calon Murid</h6>
    </div>

    <div class="col-md-8">
        <label class="form-label">1. Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama_lengkap" class="form-control input-uppercase" required
               autocomplete="name" value="<?php echo formOld($d, 'nama_lengkap'); ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">2. Jenis Kelamin <span class="text-danger">*</span></label>
        <div class="d-flex gap-3 pt-2">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_l" value="Laki-laki" required <?php echo formChecked($d, 'jenis_kelamin', 'Laki-laki'); ?>>
                <label class="form-check-label" for="jk_l">Laki-laki</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_p" value="Perempuan" <?php echo formChecked($d, 'jenis_kelamin', 'Perempuan'); ?>>
                <label class="form-check-label" for="jk_p">Perempuan</label>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">3. NISN</label>
        <input type="text" name="nisn" class="form-control" maxlength="10" value="<?php echo formOld($d, 'nisn'); ?>">
    </div>

    <div class="col-12">
        <label class="form-label d-block">Pernah TK Atau Belum <span class="text-danger">*</span></label>
        <?php
        $pernahTk = $d['pernah_tk'] ?? '';
        ?>
        <div class="d-flex flex-wrap gap-4 pt-1">
            <div class="form-check">
                <input class="form-check-input pernah-tk-radio" type="radio" name="pernah_tk" id="pernah_tk_ya" value="Ya" required
                    <?php echo formChecked($d, 'pernah_tk', 'Ya'); ?>>
                <label class="form-check-label" for="pernah_tk_ya">Ya</label>
            </div>
            <div class="form-check">
                <input class="form-check-input pernah-tk-radio" type="radio" name="pernah_tk" id="pernah_tk_tidak" value="Tidak"
                    <?php echo formChecked($d, 'pernah_tk', 'Tidak'); ?>>
                <label class="form-check-label" for="pernah_tk_tidak">Tidak</label>
            </div>
        </div>
    </div>

    <div class="col-md-8 wrap-nama-tk" id="wrapNamaTk" style="<?php echo $pernahTk === 'Ya' ? '' : 'display:none;'; ?>">
        <label class="form-label">Nama TK <span class="text-danger">*</span></label>
        <input type="text" name="nama_tk" id="inputNamaTk" class="form-control input-uppercase" maxlength="150"
               placeholder="Tuliskan nama TK / PAUD"
               value="<?php echo formOld($d, 'nama_tk'); ?>"
               <?php echo $pernahTk === 'Ya' ? 'required' : ''; ?>>
    </div>

    <div class="col-md-4">
        <label class="form-label">4. NIK</label>
        <input type="text" name="nik" class="form-control" maxlength="16" value="<?php echo formOld($d, 'nik'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">5. No. KK</label>
        <input type="text" name="no_kk" class="form-control" maxlength="16" value="<?php echo formOld($d, 'no_kk'); ?>">
    </div>

    <div class="col-md-6">
        <label class="form-label">6. Tempat Lahir</label>
        <input type="text" name="tempat_lahir" class="form-control" value="<?php echo formOld($d, 'tempat_lahir'); ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">7. Tanggal Lahir</label>
        <input type="text" name="tanggal_lahir" class="form-control" placeholder="07 Juni 2026" value="<?php echo htmlspecialchars(isset($d['tanggal_lahir']) ? format_date_indonesia($d['tanggal_lahir']) : ''); ?>">
    </div>

    <div class="col-md-4">
        <label class="form-label">8. Agama <span class="text-danger">*</span></label>
        <select name="agama" class="form-select" required>
            <option value="">-- Pilih --</option>
            <?php foreach (['Kristen', 'Katolik', 'Islam', 'Hindu', 'Buddha', 'Konghucu'] as $ag): ?>
            <option value="<?php echo $ag; ?>" <?php echo formSelected($d, 'agama', $ag); ?>><?php echo $ag; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">9. Berkebutuhan Khusus</label>
        <select name="berkebutuhan_khusus" class="form-select">
            <?php foreach (['Tidak', 'Tuna Netra', 'Tuna Rungu', 'Tuna Grahita', 'Tuna Daksa', 'Tuna Wicara', 'Tuna Laras', 'Lainnya'] as $bk): ?>
            <option value="<?php echo $bk; ?>" <?php echo formSelected($d, 'berkebutuhan_khusus', $bk); ?>><?php echo $bk; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">16. Anak Keberapa</label>
        <input type="number" name="anak_keberapa" class="form-control" min="1" max="20" value="<?php echo formOld($d, 'anak_keberapa'); ?>">
    </div>

    <div class="col-12">
        <label class="form-label">10. Alamat</label>
        <textarea name="alamat" class="form-control" rows="2"><?php echo formOld($d, 'alamat'); ?></textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">11. Dusun</label>
        <input type="text" name="dusun" class="form-control" value="<?php echo formOld($d, 'dusun'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">12. Kelurahan/Desa</label>
        <input type="text" name="kelurahan" class="form-control" value="<?php echo formOld($d, 'kelurahan'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">13. Kecamatan</label>
        <input type="text" name="kecamatan" class="form-control" value="<?php echo formOld($d, 'kecamatan'); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">14. Kode Pos</label>
        <input type="text" name="kode_pos" class="form-control" maxlength="10" value="<?php echo formOld($d, 'kode_pos'); ?>">
    </div>
    <div class="col-md-8">
        <label class="form-label">15. Moda Transportasi</label>
        <select name="moda_transportasi" class="form-select">
            <option value="">-- Pilih --</option>
            <?php
            $transport = ['Jalan Kaki', 'Sepeda', 'Sepeda Motor', 'Mobil Pribadi', 'Antar Jemput Sekolah', 'Angkutan Umum', 'Lainnya'];
            foreach ($transport as $tr):
            ?>
            <option value="<?php echo $tr; ?>" <?php echo formSelected($d, 'moda_transportasi', $tr); ?>><?php echo $tr; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-actions form-actions-step1">
        <div class="form-actions-left">
            <button type="submit" name="action" value="draft_step1" class="btn btn-warning btn-simpan-draft" formnovalidate>
                <i class="bi bi-save"></i> Simpan Draft
            </button>
        </div>
        <div class="form-actions-right">
            <button type="submit" name="action" value="save_step1" class="btn btn-primary btn-selanjutnya">
                Selanjutnya <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</form>
<script>
(function () {
    var radios = document.querySelectorAll('.pernah-tk-radio');
    var wrap = document.getElementById('wrapNamaTk');
    var input = document.getElementById('inputNamaTk');
    if (!radios.length || !wrap || !input) return;

    function updateNamaTk() {
        var ya = document.getElementById('pernah_tk_ya');
        if (ya && ya.checked) {
            wrap.style.display = '';
            input.setAttribute('required', 'required');
        } else {
            wrap.style.display = 'none';
            input.removeAttribute('required');
            input.value = '';
        }
    }

    radios.forEach(function (r) {
        r.addEventListener('change', updateNamaTk);
    });
})();
</script>
