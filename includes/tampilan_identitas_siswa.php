<?php
/**
 * Tampilan lengkap identitas siswa (semua field formulir).
 * Variabel $siswa (array baris DB) harus sudah di-set.
 */
$dokumenList = $dokumenList ?? daftarDokumenSiswa();

function tampilNilai(?string $v): string
{
    $v = trim((string) $v);
    return $v !== '' ? htmlspecialchars($v) : '<span class="text-muted">—</span>';
}

require_once __DIR__ . '/helpers_date.php';

function tampilTanggal(?string $v): string
{
    if ($v === null || $v === '' || $v === '0000-00-00') {
        return '<span class="text-muted">—</span>';
    }
    $ts = strtotime($v);
    return $ts ? format_date_indonesia(date('Y-m-d', $ts)) : tampilNilai($v);
}
?>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card content-card h-100">
            <div class="card-header">Data Pribadi & Alamat</div>
            <div class="card-body">
                <table class="table table-sm table-borderless identitas-table mb-0">
                    <tr><td class="text-muted">Tanggal Pendaftaran</td><td><?php echo tampilTanggal($siswa['tanggal_pendaftaran'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Petugas Pendaftar</td><td><?php echo tampilNilai(namaPetugasPendaftar($siswa)); ?></td></tr>
                    <tr><td class="text-muted">NISN</td><td><?php echo tampilNilai($siswa['nisn'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Pernah TK</td><td><?php echo tampilNilai($siswa['pernah_tk'] ?? ''); ?></td></tr>
                    <?php if (($siswa['pernah_tk'] ?? '') === 'Ya'): ?>
                    <tr><td class="text-muted">Nama TK</td><td><?php echo tampilNilai($siswa['nama_tk'] ?? ''); ?></td></tr>
                    <?php endif; ?>
                    <tr><td class="text-muted">Nama Lengkap</td><td><strong><?php echo tampilNilai($siswa['nama_lengkap'] ?? ''); ?></strong></td></tr>
                    <tr><td class="text-muted">Jenis Kelamin</td><td><?php echo tampilNilai($siswa['jenis_kelamin'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Agama</td><td><?php echo tampilNilai($siswa['agama'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">NIK</td><td><?php echo tampilNilai($siswa['nik'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">No. KK</td><td><?php echo tampilNilai($siswa['no_kk'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Tempat, Tgl Lahir</td><td><?php
                        $ttl = trim(($siswa['tempat_lahir'] ?? '') . ', ' . ($siswa['tanggal_lahir'] ?? ''));
                        echo $ttl !== ',' ? (htmlspecialchars($siswa['tempat_lahir'] ?? '-') . ', ' . ($siswa['tanggal_lahir'] ? date('d/m/Y', strtotime($siswa['tanggal_lahir'])) : '-')) : '<span class="text-muted">—</span>';
                    ?></td></tr>
                    <tr><td class="text-muted">Berkebutuhan Khusus</td><td><?php echo tampilNilai($siswa['berkebutuhan_khusus'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Alamat</td><td><?php echo tampilNilai($siswa['alamat'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Dusun / Kelurahan</td><td><?php echo tampilNilai(($siswa['dusun'] ?? '') . ($siswa['kelurahan'] ? ', ' . $siswa['kelurahan'] : '')); ?></td></tr>
                    <tr><td class="text-muted">Kecamatan / Kode Pos</td><td><?php echo tampilNilai(($siswa['kecamatan'] ?? '') . ($siswa['kode_pos'] ? ' — ' . $siswa['kode_pos'] : '')); ?></td></tr>
                    <tr><td class="text-muted">Transportasi</td><td><?php echo tampilNilai($siswa['moda_transportasi'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Anak Ke-</td><td><?php echo tampilNilai($siswa['anak_keberapa'] ?? ''); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card content-card h-100">
            <div class="card-header">Orang Tua, Kontak & Periodik</div>
            <div class="card-body">
                <table class="table table-sm table-borderless identitas-table mb-0">
                    <tr><td colspan="2" class="fw-semibold text-secondary pt-0">Ayah</td></tr>
                    <tr><td class="text-muted" width="38%">Nama Ayah</td><td><?php echo tampilNilai($siswa['nama_ayah'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">NIK / Th. Lahir</td><td><?php echo tampilNilai(($siswa['nik_ayah'] ?? '-') . ' / ' . ($siswa['tahun_lahir_ayah'] ?? '-')); ?></td></tr>
                    <tr><td class="text-muted">Pendidikan / Pekerjaan</td><td><?php echo tampilNilai(($siswa['pendidikan_ayah'] ?? '-') . ' / ' . ($siswa['pekerjaan_ayah'] ?? '-')); ?></td></tr>
                    <tr><td colspan="2" class="fw-semibold text-secondary">Ibu</td></tr>
                    <tr><td class="text-muted">Nama Ibu</td><td><?php echo tampilNilai($siswa['nama_ibu'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">NIK / Th. Lahir</td><td><?php echo tampilNilai(($siswa['nik_ibu'] ?? '-') . ' / ' . ($siswa['tahun_lahir_ibu'] ?? '-')); ?></td></tr>
                    <tr><td class="text-muted">Pendidikan / Pekerjaan</td><td><?php echo tampilNilai(($siswa['pendidikan_ibu'] ?? '-') . ' / ' . ($siswa['pekerjaan_ibu'] ?? '-')); ?></td></tr>
                    <tr><td colspan="2" class="fw-semibold text-secondary">Wali (jika ada)</td></tr>
                    <tr><td class="text-muted">Nama Wali</td><td><?php echo tampilNilai($siswa['nama_wali'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">No. HP / Email</td><td><?php echo tampilNilai(($siswa['no_hp'] ?? '-') . ' / ' . ($siswa['email'] ?? '-')); ?></td></tr>
                    <tr><td colspan="2" class="fw-semibold text-secondary">Periodik</td></tr>
                    <tr><td class="text-muted">Tinggi / Berat</td><td><?php echo tampilNilai(($siswa['tinggi_badan'] ?? '-') . ' cm / ' . ($siswa['berat_badan'] ?? '-') . ' kg'); ?></td></tr>
                    <tr><td class="text-muted">Lingkar Kepala</td><td><?php echo tampilNilai(($siswa['lingkar_kepala'] ?? '') !== '' ? $siswa['lingkar_kepala'] . ' cm' : ''); ?></td></tr>
                    <tr><td class="text-muted">Jarak / Waktu</td><td><?php echo tampilNilai(($siswa['jarak_sekolah'] ?? '-') . ' km / ' . ($siswa['waktu_tempuh'] ?? '-')); ?></td></tr>
                    <tr><td class="text-muted">Jumlah Saudara</td><td><?php echo tampilNilai($siswa['jumlah_saudara'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Hobi</td><td><?php echo tampilNilai($siswa['hobi'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Cita-cita</td><td><?php echo tampilNilai($siswa['cita_cita'] ?? ''); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card content-card">
            <div class="card-header">Berkas / Dokumen</div>
            <div class="card-body">
                <div class="row g-2">
                    <?php foreach ($dokumenList as $field => $info):
                        $path = $siswa[$field] ?? '';
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                            <span class="small"><?php echo htmlspecialchars($info['label']); ?></span>
                            <?php if ($path !== ''): ?>
                            <a href="<?php echo htmlspecialchars($path); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                            <?php else: ?>
                            <span class="text-muted small">Belum ada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
