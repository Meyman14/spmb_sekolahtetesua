<?php
require_once __DIR__ . '/../helpers_date.php';
$iso = $d['tanggal_pendaftaran'] ?? '';
if (empty($iso) && !empty($d['tgl_hari']) && !empty($d['tgl_bulan']) && !empty($d['tgl_tahun'])) {
    $iso = sprintf('%04d-%02d-%02d', $d['tgl_tahun'], $d['tgl_bulan'], $d['tgl_hari']);
}
$displayTanggal = '';
if (!empty($iso)) {
    $displayTanggal = format_date_indonesia($iso);
}
?>
<div class="formulir-official-header">
    <div class="formulir-meta row align-items-center g-2">
        <div class="col-md-8">
            <div class="tanggal-pendaftaran-wrap">
                <span class="tanggal-label">Tanggal:</span>
                <div class="tanggal-input-group" role="group" aria-label="Tanggal pendaftaran">
                    <input type="text" name="tanggal_pendaftaran" class="form-control" placeholder="07 Juni 2026"
                           value="<?php echo htmlspecialchars($displayTanggal); ?>" required>
                </div>
                <small class="text-danger d-block mt-1">* Wajib diisi — tanggal siswa mendaftar</small>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="formulir-code">F-PD</div>
        </div>
    </div>
</div>
<hr class="formulir-divider">
