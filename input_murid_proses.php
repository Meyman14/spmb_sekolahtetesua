<?php
require_once 'includes/auth.php';
require_once 'koneksi.php';
require_once 'includes/form_pendaftaran.php';
require_once 'includes/ensure_siswa_schema.php';
require_once 'includes/dokumen.php';
require_once 'includes/siswa_draft_db.php';

requirePetugas();
ensureSiswaSchema($conn);

if (isset($_POST['edit_id']) && (int) $_POST['edit_id'] > 0) {
    formEditSet((int) $_POST['edit_id']);
}

$editId = formEditId();

$redirectError = function (int $step, string $msg) {
    $_SESSION['form_error'] = $msg;
    header('Location: ' . formMuridUrl($step));
    exit;
};

$action = $_POST['action'] ?? '';

// DEBUG: rekam data submit untuk investigasi masalah alur step (hapus setelah debugging)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbg = [
        'time' => date('c'),
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
        'action' => $action,
        'post_keys' => array_keys($_POST),
        'post' => $_POST,
        'files' => array_map(function($f){ return $f['name'] ?? null; }, $_FILES),
        'session_keys' => array_keys($_SESSION ?? []),
    ];
    $logDir = __DIR__ . '/uploads';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    @file_put_contents($logDir . '/submit_debug_' . time() . '.json', json_encode($dbg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/** Kumpulkan data tahap 1 dari POST */
function kumpulkanDataStep1DariPost(): array
{
    $tglParts = parseTanggalPendaftaranDariPost();
    $tanggalPendaftaran = validasiTanggalPendaftaran($tglParts);

    $data = sanitizePost([
        'nama_lengkap', 'jenis_kelamin', 'nisn', 'pernah_tk', 'nama_tk', 'nik', 'no_kk', 'tempat_lahir',
        'tanggal_lahir', 'agama', 'berkebutuhan_khusus', 'alamat', 'dusun',
        'kelurahan', 'kecamatan', 'kode_pos', 'moda_transportasi', 'anak_keberapa',
    ]);
    normalisasiDataTk($data);
    normalisasiNamaKapitalPadaData($data, NAMA_KAPITAL_FIELDS_STEP1);

    if ($tanggalPendaftaran === null) {
        $tanggalPendaftaran = date('Y-m-d');
    }

    $data['tanggal_pendaftaran'] = $tanggalPendaftaran;
    $data['tgl_hari'] = $tglParts['hari'] ?: date('d');
    $data['tgl_bulan'] = $tglParts['bulan'] ?: date('m');
    $data['tgl_tahun'] = $tglParts['tahun'] ?: date('Y');

    return $data;
}

if ($action === 'draft_step1') {
    $data = kumpulkanDataStep1DariPost();
    formSessionSet('step1', $data);

    try {
        $draftId = upsertDraftSiswa($conn, $data, [], [], $editId, 'draft');
        formEditSet($draftId);
    } catch (Throwable $e) {
        $redirectError(1, 'Gagal menyimpan draft: ' . $e->getMessage());
    }

    $_SESSION['form_success'] = 'Draft tahap 1 berhasil disimpan. Anda dapat melanjutkan atau kembali lagi nanti.';
    header('Location: ' . formMuridUrl(1));
    exit;
}

if ($action === 'save_step1') {
    $tglParts = parseTanggalPendaftaranDariPost();
    $tanggalPendaftaran = validasiTanggalPendaftaran($tglParts);

    $data = sanitizePost([
        'nama_lengkap', 'jenis_kelamin', 'nisn', 'pernah_tk', 'nama_tk', 'nik', 'no_kk', 'tempat_lahir',
        'tanggal_lahir', 'agama', 'berkebutuhan_khusus', 'alamat', 'dusun',
        'kelurahan', 'kecamatan', 'kode_pos', 'moda_transportasi', 'anak_keberapa',
    ]);
    normalisasiDataTk($data);
    normalisasiNamaKapitalPadaData($data, NAMA_KAPITAL_FIELDS_STEP1);

    if ($tanggalPendaftaran === null) {
        $redirectError(1, 'Tanggal pendaftaran wajib diisi dengan format yang benar (contoh: 07 Juni 2026).');
    }

    if ($data['nama_lengkap'] === '' || $data['jenis_kelamin'] === '' || $data['agama'] === '') {
        $redirectError(1, 'Nama lengkap, jenis kelamin, dan agama wajib diisi.');
    }

    $errTk = validasiDataTk($data);
    if ($errTk !== null) {
        $redirectError(1, $errTk);
    }

    $data['tanggal_pendaftaran'] = $tanggalPendaftaran;
    $data['tgl_hari'] = $tglParts['hari'];
    $data['tgl_bulan'] = $tglParts['bulan'];
    $data['tgl_tahun'] = $tglParts['tahun'];

    formSessionSet('step1', $data);

    try {
        $draftId = upsertDraftSiswa($conn, $data, [], [], $editId, 'draft');
        formEditSet($draftId);
    } catch (Throwable $e) {
        $redirectError(1, 'Gagal menyimpan draft: ' . $e->getMessage());
    }

    header('Location: ' . formMuridUrl(2));
    exit;
}

if ($action === 'draft_step2') {
    $step1 = formSessionGet('step1');
    if (empty($step1)) {
        header('Location: ' . formMuridUrl(1));
        exit;
    }

    $data = sanitizePost([
        'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah',
        'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu',
        'nama_wali', 'nik_wali', 'tahun_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali',
        'no_hp', 'email',
    ]);
    normalisasiNamaKapitalPadaData($data, NAMA_KAPITAL_FIELDS_STEP2);

    formSessionSet('step2', $data);

    try {
        $draftId = upsertDraftSiswa($conn, $step1, $data, [], formEditId(), 'draft');
        formEditSet($draftId);
    } catch (Throwable $e) {
        $redirectError(2, 'Gagal menyimpan draft: ' . $e->getMessage());
    }

    $_SESSION['form_success'] = 'Draft tahap 2 berhasil disimpan.';
    header('Location: ' . formMuridUrl(2));
    exit;
}

if ($action === 'save_step2') {
    if (empty(formSessionGet('step1'))) {
        header('Location: ' . formMuridUrl(1));
        exit;
    }

    $step1 = formSessionGet('step1');

    $data = sanitizePost([
        'nama_ayah', 'nik_ayah', 'tahun_lahir_ayah', 'pendidikan_ayah', 'pekerjaan_ayah',
        'nama_ibu', 'nik_ibu', 'tahun_lahir_ibu', 'pendidikan_ibu', 'pekerjaan_ibu',
        'nama_wali', 'nik_wali', 'tahun_lahir_wali', 'pendidikan_wali', 'pekerjaan_wali',
        'no_hp', 'email',
    ]);

    normalisasiNamaKapitalPadaData($data, NAMA_KAPITAL_FIELDS_STEP2);

    if ($data['nama_ayah'] === '' || $data['nama_ibu'] === '') {
        $redirectError(2, 'Nama ayah dan nama ibu wajib diisi.');
    }

    formSessionSet('step2', $data);

    try {
        $draftId = upsertDraftSiswa($conn, $step1, $data, [], formEditId(), 'draft');
        formEditSet($draftId);
    } catch (Throwable $e) {
        $redirectError(2, 'Gagal menyimpan draft: ' . $e->getMessage());
    }

    header('Location: ' . formMuridUrl(3));
    exit;
}

if ($action === 'submit_draft' || $action === 'submit_final') {
    $status = ($action === 'submit_final') ? 'final' : 'draft';

    $step1 = formSessionGet('step1');
    $step2 = formSessionGet('step2');

    if (empty($step1) || empty($step2)) {
        header('Location: ' . formMuridUrl(1));
        exit;
    }

    $step3 = sanitizePost([
        'tinggi_badan', 'berat_badan', 'lingkar_kepala', 'jarak_sekolah',
        'waktu_tempuh', 'jumlah_saudara', 'hobi', 'cita_cita',
    ]);
    formSessionSet('step3', array_merge(formSessionGet('step3'), $step3));

    $fileFields = [
        'file_kk' => 'kk',
        'file_akte' => 'akte',
        'file_ijazah' => 'ijazah',
        'file_kks' => 'kks',
        'file_foto' => 'foto',
        'file_surat_orangtua' => 'surat',
    ];

    $existingRow = null;
    if ($editId !== null) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status = 'draft' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $editId);
        mysqli_stmt_execute($stmt);
        $existingRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        if (!$existingRow) {
            $redirectError(3, 'Data draft tidak ditemukan.');
        }
    }

    // Jika sedang edit draft dan ada file pada baris existing, simpan jalur berkas ke sesi
    if ($existingRow) {
        $curr3 = formSessionGet('step3');
        foreach ($fileFields as $inputName => $prefix) {
            if (!empty($existingRow[$inputName]) && empty($curr3[$inputName . '_existing'])) {
                $curr3[$inputName . '_existing'] = $existingRow[$inputName];
            }
        }
        formSessionSet('step3', $curr3);
    }

    normalisasiDataTk($step1);
    normalisasiNamaKapitalPadaData($step1, NAMA_KAPITAL_FIELDS_STEP1);
    normalisasiNamaKapitalPadaData($step2, NAMA_KAPITAL_FIELDS_STEP2);
    $errTkFinal = validasiDataTk($step1);
    if ($errTkFinal !== null) {
        $redirectError(1, $errTkFinal);
    }

    if ($status === 'final') {
        $wajibStep1 = ['nisn', 'nik', 'no_kk', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'kelurahan', 'kecamatan'];
        foreach ($wajibStep1 as $field) {
            if (($step1[$field] ?? '') === '') {
                $redirectError(3, 'Untuk Simpan Final, lengkapi semua data pribadi wajib (NISN, NIK, alamat, dll).');
            }
        }
        if (($step2['no_hp'] ?? '') === '') {
            $redirectError(3, 'Untuk Simpan Final, nomor HP wajib diisi.');
        }
        if ($step3['tinggi_badan'] === '' || $step3['berat_badan'] === '') {
            $redirectError(3, 'Untuk Simpan Final, tinggi dan berat badan wajib diisi.');
        }
    }

    $uploaded = [];
    foreach ($fileFields as $inputName => $prefix) {
        if (empty($_FILES[$inputName]['name'])) {
            continue;
        }
        $result = uploadBerkas($_FILES[$inputName], $prefix);
        if ($result === false) {
            // Jangan hapus data sesi; biarkan pengguna melihat yang sudah terisi.
            $curr3 = formSessionGet('step3');
            // Jika ada file yang berhasil terunggah sebelumnya, simpan ke sesi agar tetap terlihat
            foreach ($uploaded as $k => $v) {
                $curr3[$k . '_existing'] = $v;
            }
            formSessionSet('step3', $curr3);

            $redirectError(3, 'Gagal mengunggah berkas. Pastikan format JPG/PNG/PDF dan ukuran maks. 2 MB.');
        }
        if (is_string($result)) {
            $uploaded[$inputName] = $result;
            // simpan juga ke sesi sebagai existing agar form menampilkan file yang sudah diunggah
            $curr3 = formSessionGet('step3');
            $curr3[$inputName . '_existing'] = $result;
            formSessionSet('step3', $curr3);
        }
    }

    $finalFiles = [];
    foreach ($fileFields as $inputName => $prefix) {
        if (!empty($uploaded[$inputName])) {
            $finalFiles[$inputName] = $uploaded[$inputName];
        } elseif ($existingRow && !empty($existingRow[$inputName])) {
            $finalFiles[$inputName] = $existingRow[$inputName];
        } else {
            $finalFiles[$inputName] = '';
        }
    }

    if ($status === 'final') {
        foreach (array_keys($fileFields) as $inputName) {
            if (empty($finalFiles[$inputName])) {
                $redirectError(3, 'Untuk Simpan Final, semua berkas wajib diunggah.');
            }
        }
    }

    $petugas = petugasSaatIni();

    if ($editId !== null) {
        $sql = "UPDATE siswa SET
            tanggal_pendaftaran = ?, petugas_username = ?, petugas_nama = ?,
            nama_lengkap = ?, jenis_kelamin = ?, nisn = ?, pernah_tk = ?, nama_tk = ?, nik = ?, no_kk = ?, tempat_lahir = ?, tanggal_lahir = ?, agama = ?,
            berkebutuhan_khusus = ?, alamat = ?, dusun = ?, kelurahan = ?, kecamatan = ?, kode_pos = ?, moda_transportasi = ?, anak_keberapa = ?,
            nama_ayah = ?, nik_ayah = ?, tahun_lahir_ayah = ?, pendidikan_ayah = ?, pekerjaan_ayah = ?,
            nama_ibu = ?, nik_ibu = ?, tahun_lahir_ibu = ?, pendidikan_ibu = ?, pekerjaan_ibu = ?,
            nama_wali = ?, nik_wali = ?, tahun_lahir_wali = ?, pendidikan_wali = ?, pekerjaan_wali = ?,
            no_hp = ?, email = ?, tinggi_badan = ?, berat_badan = ?, lingkar_kepala = ?, jarak_sekolah = ?, waktu_tempuh = ?, jumlah_saudara = ?,
            hobi = ?, cita_cita = ?,
            file_kk = ?, file_akte = ?, file_ijazah = ?, file_kks = ?, file_foto = ?, file_surat_orangtua = ?, status = ?
            WHERE id = ? AND status = 'draft'";

        $params = [
            $step1['tanggal_pendaftaran'] ?? '',
            $petugas['username'],
            $petugas['nama'],
            $step1['nama_lengkap'],
            $step1['jenis_kelamin'],
            $step1['nisn'],
            $step1['pernah_tk'],
            $step1['nama_tk'],
            $step1['nik'],
            $step1['no_kk'],
            $step1['tempat_lahir'],
            $step1['tanggal_lahir'],
            $step1['agama'],
            $step1['berkebutuhan_khusus'] !== '' ? $step1['berkebutuhan_khusus'] : 'Tidak',
            $step1['alamat'],
            $step1['dusun'],
            $step1['kelurahan'],
            $step1['kecamatan'],
            $step1['kode_pos'],
            $step1['moda_transportasi'],
            $step1['anak_keberapa'],
            $step2['nama_ayah'],
            $step2['nik_ayah'],
            $step2['tahun_lahir_ayah'],
            $step2['pendidikan_ayah'],
            $step2['pekerjaan_ayah'],
            $step2['nama_ibu'],
            $step2['nik_ibu'],
            $step2['tahun_lahir_ibu'],
            $step2['pendidikan_ibu'],
            $step2['pekerjaan_ibu'],
            $step2['nama_wali'],
            $step2['nik_wali'],
            $step2['tahun_lahir_wali'],
            $step2['pendidikan_wali'],
            $step2['pekerjaan_wali'],
            $step2['no_hp'],
            $step2['email'],
            $step3['tinggi_badan'],
            $step3['berat_badan'],
            $step3['lingkar_kepala'],
            $step3['jarak_sekolah'],
            $step3['waktu_tempuh'],
            $step3['jumlah_saudara'],
            $step3['hobi'],
            $step3['cita_cita'],
            $finalFiles['file_kk'],
            $finalFiles['file_akte'],
            $finalFiles['file_ijazah'],
            $finalFiles['file_kks'],
            $finalFiles['file_foto'],
            $finalFiles['file_surat_orangtua'],
            $status,
            $editId,
        ];

        $stmt = mysqli_prepare($conn, $sql);
        stmtBindParams($stmt, $params);

        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) >= 0) {
            mysqli_stmt_close($stmt);
            if ($status === 'final') {
                resetVerifikasiDokumen($conn, $editId);
            }
            formSessionClear();

            if ($status === 'final') {
                $_SESSION['form_success'] = 'Data berhasil diperbarui dan dikirim sebagai Final. Menunggu verifikasi admin.';
                header('Location: status_final.php');
            } else {
                $_SESSION['form_success'] = 'Data draft berhasil diperbarui.';
                header('Location: status_draft.php');
            }
            exit;
        }

        $redirectError(3, 'Gagal memperbarui data: ' . mysqli_error($conn));
    }

    // Insert baru
    $params = [
        $step1['tanggal_pendaftaran'] ?? '',
        $petugas['username'],
        $petugas['nama'],
        $step1['nama_lengkap'],
        $step1['jenis_kelamin'],
        $step1['nisn'],
        $step1['pernah_tk'],
        $step1['nama_tk'],
        $step1['nik'],
        $step1['no_kk'],
        $step1['tempat_lahir'],
        $step1['tanggal_lahir'],
        $step1['agama'],
        $step1['berkebutuhan_khusus'] !== '' ? $step1['berkebutuhan_khusus'] : 'Tidak',
        $step1['alamat'],
        $step1['dusun'],
        $step1['kelurahan'],
        $step1['kecamatan'],
        $step1['kode_pos'],
        $step1['moda_transportasi'],
        $step1['anak_keberapa'],
        $step2['nama_ayah'],
        $step2['nik_ayah'],
        $step2['tahun_lahir_ayah'],
        $step2['pendidikan_ayah'],
        $step2['pekerjaan_ayah'],
        $step2['nama_ibu'],
        $step2['nik_ibu'],
        $step2['tahun_lahir_ibu'],
        $step2['pendidikan_ibu'],
        $step2['pekerjaan_ibu'],
        $step2['nama_wali'],
        $step2['nik_wali'],
        $step2['tahun_lahir_wali'],
        $step2['pendidikan_wali'],
        $step2['pekerjaan_wali'],
        $step2['no_hp'],
        $step2['email'],
        $step3['tinggi_badan'],
        $step3['berat_badan'],
        $step3['lingkar_kepala'],
        $step3['jarak_sekolah'],
        $step3['waktu_tempuh'],
        $step3['jumlah_saudara'],
        $step3['hobi'],
        $step3['cita_cita'],
        $finalFiles['file_kk'],
        $finalFiles['file_akte'],
        $finalFiles['file_ijazah'],
        $finalFiles['file_kks'],
        $finalFiles['file_foto'],
        $finalFiles['file_surat_orangtua'],
        $status,
    ];

    $placeholders = implode(',', array_fill(0, count($params), '?'));
    $sql = "INSERT INTO siswa (
        tanggal_pendaftaran, petugas_username, petugas_nama, nama_lengkap, jenis_kelamin, nisn, pernah_tk, nama_tk, nik, no_kk, tempat_lahir, tanggal_lahir, agama,
        berkebutuhan_khusus, alamat, dusun, kelurahan, kecamatan, kode_pos, moda_transportasi, anak_keberapa,
        nama_ayah, nik_ayah, tahun_lahir_ayah, pendidikan_ayah, pekerjaan_ayah,
        nama_ibu, nik_ibu, tahun_lahir_ibu, pendidikan_ibu, pekerjaan_ibu,
        nama_wali, nik_wali, tahun_lahir_wali, pendidikan_wali, pekerjaan_wali,
        no_hp, email, tinggi_badan, berat_badan, lingkar_kepala, jarak_sekolah, waktu_tempuh, jumlah_saudara,
        hobi, cita_cita,
        file_kk, file_akte, file_ijazah, file_kks, file_foto, file_surat_orangtua, status
    ) VALUES ($placeholders)";

    $stmt = mysqli_prepare($conn, $sql);
    stmtBindParams($stmt, $params);

    if (mysqli_stmt_execute($stmt)) {
        $newId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        resetVerifikasiDokumen($conn, $newId);
        formSessionClear();

        if ($status === 'final') {
            $_SESSION['form_success'] = 'Data berhasil disimpan sebagai Final. Menunggu verifikasi admin.';
            header('Location: status_final.php');
        } else {
            $_SESSION['form_success'] = 'Data berhasil disimpan sebagai Draft. Anda dapat melengkapi atau mengedit data ini nanti.';
            header('Location: status_draft.php');
        }
        exit;
    }

    $redirectError(3, 'Gagal menyimpan ke database: ' . mysqli_error($conn));
}

header('Location: input_murid.php?baru=1');
exit;
