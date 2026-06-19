<?php

require_once __DIR__ . '/form_pendaftaran.php';

/**
 * Kolom ekspor Excel — urutan sesuai formulir pendaftaran.
 *
 * @return list<array{key: string, label: string, format?: string}>
 */
function exportExcelColumns(): array
{
    return [
        ['key' => 'nama_lengkap', 'label' => 'Nama Lengkap'],
        ['key' => 'jenis_kelamin', 'label' => 'Jenis Kelamin'],
        ['key' => 'nisn', 'label' => 'NISN', 'format' => 'text'],
        ['key' => 'pernah_tk', 'label' => 'Pernah TK Atau Belum'],
        ['key' => 'nama_tk', 'label' => 'Nama TK'],
        ['key' => 'nik', 'label' => 'NIK', 'format' => 'text'],
        ['key' => 'no_kk', 'label' => 'No. KK', 'format' => 'text'],
        ['key' => 'tempat_lahir', 'label' => 'Tempat Lahir'],
        ['key' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'format' => 'date'],
        ['key' => 'agama', 'label' => 'Agama'],
        ['key' => 'berkebutuhan_khusus', 'label' => 'Berkebutuhan Khusus'],
        ['key' => 'alamat', 'label' => 'Alamat'],
        ['key' => 'dusun', 'label' => 'Dusun'],
        ['key' => 'kelurahan', 'label' => 'Kelurahan/Desa'],
        ['key' => 'kecamatan', 'label' => 'Kecamatan'],
        ['key' => 'kode_pos', 'label' => 'Kode Pos', 'format' => 'text'],
        ['key' => 'moda_transportasi', 'label' => 'Moda Transportasi'],
        ['key' => 'anak_keberapa', 'label' => 'Anak Keberapa'],
        ['key' => 'nama_ayah', 'label' => 'Nama Ayah'],
        ['key' => 'nik_ayah', 'label' => 'NIK Ayah', 'format' => 'text'],
        ['key' => 'tahun_lahir_ayah', 'label' => 'Tahun Lahir Ayah'],
        ['key' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah'],
        ['key' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah'],
        ['key' => 'nama_ibu', 'label' => 'Nama Ibu'],
        ['key' => 'nik_ibu', 'label' => 'NIK Ibu', 'format' => 'text'],
        ['key' => 'tahun_lahir_ibu', 'label' => 'Tahun Lahir Ibu'],
        ['key' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu'],
        ['key' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu'],
        ['key' => 'nama_wali', 'label' => 'Nama Wali'],
        ['key' => 'nik_wali', 'label' => 'NIK Wali', 'format' => 'text'],
        ['key' => 'tahun_lahir_wali', 'label' => 'Tahun Lahir Wali'],
        ['key' => 'pendidikan_wali', 'label' => 'Pendidikan Wali'],
        ['key' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali'],
        ['key' => 'no_hp', 'label' => 'No. HP', 'format' => 'text'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'tinggi_badan', 'label' => 'Tinggi Badan'],
        ['key' => 'berat_badan', 'label' => 'Berat Badan'],
        ['key' => 'lingkar_kepala', 'label' => 'Lingkar Kepala'],
        ['key' => 'jarak_sekolah', 'label' => 'Jarak ke Sekolah'],
        ['key' => 'waktu_tempuh', 'label' => 'Waktu Tempuh'],
        ['key' => 'jumlah_saudara', 'label' => 'Jumlah Saudara'],
    ];
}

function exportExcelCellValue(array $row, array $col): string
{
    $value = $row[$col['key']] ?? '';

    if ($value === null || $value === '') {
        return '';
    }

    if (($col['format'] ?? '') === 'date') {
        $ts = strtotime((string) $value);

        return $ts ? date('d/m/Y', $ts) : (string) $value;
    }

    if (in_array($col['key'], ['nama_lengkap', 'nama_ayah', 'nama_ibu', 'nama_tk'], true)) {
        return normalisasiNamaKapital((string) $value);
    }

    return (string) $value;
}

function exportExcelIsTextColumn(array $col): bool
{
    return ($col['format'] ?? '') === 'text';
}
