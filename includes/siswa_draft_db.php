<?php

require_once __DIR__ . '/form_pendaftaran.php';
require_once __DIR__ . '/role.php';

/**
 * Simpan / perbarui baris draft di database (agar data tidak hilang antar tahap).
 *
 * @return int ID siswa
 */
function upsertDraftSiswa(mysqli $conn, array $step1, array $step2 = [], array $step3 = [], ?int $editId = null, string $status = 'draft'): int
{
    $petugas = petugasSaatIni();
    normalisasiDataTk($step1);
    normalisasiNamaKapitalPadaData($step1, NAMA_KAPITAL_FIELDS_STEP1);
    if (!empty($step2)) {
        normalisasiNamaKapitalPadaData($step2, NAMA_KAPITAL_FIELDS_STEP2);
    }

    $fields = [
        'tanggal_pendaftaran' => $step1['tanggal_pendaftaran'] ?? null,
        'petugas_username' => $petugas['username'],
        'petugas_nama' => $petugas['nama'],
        'nama_lengkap' => $step1['nama_lengkap'] ?? '',
        'jenis_kelamin' => $step1['jenis_kelamin'] ?? '',
        'nisn' => $step1['nisn'] ?? '',
        'pernah_tk' => $step1['pernah_tk'] ?? '',
        'nama_tk' => $step1['nama_tk'] ?? '',
        'nik' => $step1['nik'] ?? '',
        'no_kk' => $step1['no_kk'] ?? '',
        'tempat_lahir' => $step1['tempat_lahir'] ?? '',
        'tanggal_lahir' => $step1['tanggal_lahir'] ?: null,
        'agama' => $step1['agama'] ?? '',
        'berkebutuhan_khusus' => ($step1['berkebutuhan_khusus'] ?? '') !== '' ? $step1['berkebutuhan_khusus'] : 'Tidak',
        'alamat' => $step1['alamat'] ?? '',
        'dusun' => $step1['dusun'] ?? '',
        'kelurahan' => $step1['kelurahan'] ?? '',
        'kecamatan' => $step1['kecamatan'] ?? '',
        'kode_pos' => $step1['kode_pos'] ?? '',
        'moda_transportasi' => $step1['moda_transportasi'] ?? '',
        'anak_keberapa' => $step1['anak_keberapa'] ?: null,
        'nama_ayah' => $step2['nama_ayah'] ?? '',
        'nik_ayah' => $step2['nik_ayah'] ?? '',
        'tahun_lahir_ayah' => $step2['tahun_lahir_ayah'] ?: null,
        'pendidikan_ayah' => $step2['pendidikan_ayah'] ?? '',
        'pekerjaan_ayah' => $step2['pekerjaan_ayah'] ?? '',
        'nama_ibu' => $step2['nama_ibu'] ?? '',
        'nik_ibu' => $step2['nik_ibu'] ?? '',
        'tahun_lahir_ibu' => $step2['tahun_lahir_ibu'] ?: null,
        'pendidikan_ibu' => $step2['pendidikan_ibu'] ?? '',
        'pekerjaan_ibu' => $step2['pekerjaan_ibu'] ?? '',
        'nama_wali' => $step2['nama_wali'] ?? '',
        'nik_wali' => $step2['nik_wali'] ?? '',
        'tahun_lahir_wali' => $step2['tahun_lahir_wali'] ?: null,
        'pendidikan_wali' => $step2['pendidikan_wali'] ?? '',
        'pekerjaan_wali' => $step2['pekerjaan_wali'] ?? '',
        'no_hp' => $step2['no_hp'] ?? '',
        'email' => $step2['email'] ?? '',
        'tinggi_badan' => $step3['tinggi_badan'] ?: null,
        'berat_badan' => $step3['berat_badan'] ?: null,
        'lingkar_kepala' => $step3['lingkar_kepala'] ?: null,
        'jarak_sekolah' => $step3['jarak_sekolah'] ?: null,
        'waktu_tempuh' => $step3['waktu_tempuh'] ?? '',
        'jumlah_saudara' => $step3['jumlah_saudara'] ?: null,
        'hobi' => $step3['hobi'] ?? '',
        'cita_cita' => $step3['cita_cita'] ?? '',
        'status' => $status,
    ];

    if ($editId !== null && $editId > 0) {
        $existing = null;
        $stmt = mysqli_prepare($conn, "SELECT file_kk, file_akte, file_ijazah, file_kks, file_foto, file_surat_orangtua FROM siswa WHERE id = ? AND status = 'draft' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$existing) {
            throw new RuntimeException('Draft tidak ditemukan.');
        }

        $sql = "UPDATE siswa SET
            tanggal_pendaftaran=?, petugas_username=?, petugas_nama=?,
            nama_lengkap=?, jenis_kelamin=?, nisn=?, pernah_tk=?, nama_tk=?, nik=?, no_kk=?, tempat_lahir=?, tanggal_lahir=?, agama=?,
            berkebutuhan_khusus=?, alamat=?, dusun=?, kelurahan=?, kecamatan=?, kode_pos=?, moda_transportasi=?, anak_keberapa=?,
            nama_ayah=?, nik_ayah=?, tahun_lahir_ayah=?, pendidikan_ayah=?, pekerjaan_ayah=?,
            nama_ibu=?, nik_ibu=?, tahun_lahir_ibu=?, pendidikan_ibu=?, pekerjaan_ibu=?,
            nama_wali=?, nik_wali=?, tahun_lahir_wali=?, pendidikan_wali=?, pekerjaan_wali=?,
            no_hp=?, email=?, tinggi_badan=?, berat_badan=?, lingkar_kepala=?, jarak_sekolah=?, waktu_tempuh=?, jumlah_saudara=?,
            hobi=?, cita_cita=?, status=?
            WHERE id=? AND status='draft'";

        $params = array_values($fields);
        $params[] = $editId;
        $stmt = mysqli_prepare($conn, $sql);
        stmtBindParams($stmt, $params);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $editId;
    }

    $sql = "INSERT INTO siswa (
        tanggal_pendaftaran, petugas_username, petugas_nama, nama_lengkap, jenis_kelamin, nisn, pernah_tk, nama_tk, nik, no_kk,
        tempat_lahir, tanggal_lahir, agama, berkebutuhan_khusus, alamat, dusun, kelurahan, kecamatan, kode_pos, moda_transportasi, anak_keberapa,
        nama_ayah, nik_ayah, tahun_lahir_ayah, pendidikan_ayah, pekerjaan_ayah,
        nama_ibu, nik_ibu, tahun_lahir_ibu, pendidikan_ibu, pekerjaan_ibu,
        nama_wali, nik_wali, tahun_lahir_wali, pendidikan_wali, pekerjaan_wali,
        no_hp, email, tinggi_badan, berat_badan, lingkar_kepala, jarak_sekolah, waktu_tempuh, jumlah_saudara, hobi, cita_cita, status
    ) VALUES (" . implode(',', array_fill(0, count($fields), '?')) . ")";

    $stmt = mysqli_prepare($conn, $sql);
    stmtBindParams($stmt, array_values($fields));
    mysqli_stmt_execute($stmt);
    $newId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    return $newId;
}
