<?php

const SESSION_KEY_PENDAFTARAN = 'pendaftaran_siswa';
const SESSION_KEY_EDIT_ID = 'edit_siswa_id';

function formSessionGet(string $step): array
{
    return $_SESSION[SESSION_KEY_PENDAFTARAN][$step] ?? [];
}

function formSessionSet(string $step, array $data): void
{
    if (!isset($_SESSION[SESSION_KEY_PENDAFTARAN])) {
        $_SESSION[SESSION_KEY_PENDAFTARAN] = [];
    }

    // Parse tanggal_lahir if provided in Indonesian format
    if (!empty($data['tanggal_lahir'])) {
        require_once __DIR__ . '/helpers_date.php';
        $iso = parse_date_indonesia($data['tanggal_lahir']);
        if ($iso) $data['tanggal_lahir'] = $iso;
    }
    $_SESSION[SESSION_KEY_PENDAFTARAN][$step] = $data;
}

function formSessionClear(): void
{
    unset($_SESSION[SESSION_KEY_PENDAFTARAN]);
    unset($_SESSION[SESSION_KEY_EDIT_ID]);
}

function formEditId(): ?int
{
    $id = (int) ($_SESSION[SESSION_KEY_EDIT_ID] ?? 0);

    return $id > 0 ? $id : null;
}

function formEditSet(?int $id): void
{
    if ($id !== null && $id > 0) {
        $_SESSION[SESSION_KEY_EDIT_ID] = $id;
    } else {
        unset($_SESSION[SESSION_KEY_EDIT_ID]);
    }
}

function formMuridUrl(int $step): string
{
    $url = 'input_murid.php?step=' . $step;
    $editId = formEditId();
    if ($editId !== null) {
        $url .= '&edit=' . $editId;
    }

    return $url;
}

function loadDraftKeSession(mysqli $conn, int $id): bool
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id = ? AND status = 'draft' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$row) {
        return false;
    }

    $tglParts = splitTanggalPendaftaran($row['tanggal_pendaftaran'] ?? '');

    formSessionSet('step1', [
        'tanggal_pendaftaran' => $row['tanggal_pendaftaran'] ?? '',
        'tgl_hari' => $tglParts['hari'],
        'tgl_bulan' => $tglParts['bulan'],
        'tgl_tahun' => $tglParts['tahun'],
        'nama_lengkap' => $row['nama_lengkap'] ?? '',
        'jenis_kelamin' => $row['jenis_kelamin'] ?? '',
        'nisn' => $row['nisn'] ?? '',
        'pernah_tk' => $row['pernah_tk'] ?? '',
        'nama_tk' => $row['nama_tk'] ?? '',
        'nik' => $row['nik'] ?? '',
        'no_kk' => $row['no_kk'] ?? '',
        'tempat_lahir' => $row['tempat_lahir'] ?? '',
        'tanggal_lahir' => $row['tanggal_lahir'] ?? '',
        'agama' => $row['agama'] ?? '',
        'berkebutuhan_khusus' => $row['berkebutuhan_khusus'] ?? 'Tidak',
        'alamat' => $row['alamat'] ?? '',
        'dusun' => $row['dusun'] ?? '',
        'kelurahan' => $row['kelurahan'] ?? '',
        'kecamatan' => $row['kecamatan'] ?? '',
        'kode_pos' => $row['kode_pos'] ?? '',
        'moda_transportasi' => $row['moda_transportasi'] ?? '',
        'anak_keberapa' => $row['anak_keberapa'] ?? '',
    ]);

    formSessionSet('step2', [
        'nama_ayah' => $row['nama_ayah'] ?? '',
        'nik_ayah' => $row['nik_ayah'] ?? '',
        'tahun_lahir_ayah' => $row['tahun_lahir_ayah'] ?? '',
        'pendidikan_ayah' => $row['pendidikan_ayah'] ?? '',
        'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? '',
        'nama_ibu' => $row['nama_ibu'] ?? '',
        'nik_ibu' => $row['nik_ibu'] ?? '',
        'tahun_lahir_ibu' => $row['tahun_lahir_ibu'] ?? '',
        'pendidikan_ibu' => $row['pendidikan_ibu'] ?? '',
        'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? '',
        'nama_wali' => $row['nama_wali'] ?? '',
        'nik_wali' => $row['nik_wali'] ?? '',
        'tahun_lahir_wali' => $row['tahun_lahir_wali'] ?? '',
        'pendidikan_wali' => $row['pendidikan_wali'] ?? '',
        'pekerjaan_wali' => $row['pekerjaan_wali'] ?? '',
        'no_hp' => $row['no_hp'] ?? '',
        'email' => $row['email'] ?? '',
    ]);

    formSessionSet('step3', [
        'tinggi_badan' => $row['tinggi_badan'] ?? '',
        'berat_badan' => $row['berat_badan'] ?? '',
        'lingkar_kepala' => $row['lingkar_kepala'] ?? '',
        'jarak_sekolah' => $row['jarak_sekolah'] ?? '',
        'waktu_tempuh' => $row['waktu_tempuh'] ?? '',
        'jumlah_saudara' => $row['jumlah_saudara'] ?? '',
        'hobi' => $row['hobi'] ?? '',
        'cita_cita' => $row['cita_cita'] ?? '',
        'file_kk_existing' => $row['file_kk'] ?? '',
        'file_akte_existing' => $row['file_akte'] ?? '',
        'file_ijazah_existing' => $row['file_ijazah'] ?? '',
        'file_kks_existing' => $row['file_kks'] ?? '',
        'file_foto_existing' => $row['file_foto'] ?? '',
        'file_surat_orangtua_existing' => $row['file_surat_orangtua'] ?? '',
    ]);

    formEditSet($id);

    return true;
}

/** Nama siswa / orang tua: simpan huruf kapital semua (sesuai formulir resmi). */
function normalisasiNamaKapital(?string $nama): string
{
    $nama = trim((string) $nama);
    if ($nama === '') {
        return '';
    }

    return mb_strtoupper($nama, 'UTF-8');
}

/** @param array<string, string> $data */
function normalisasiNamaKapitalPadaData(array &$data, array $keys): void
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $data)) {
            $data[$key] = normalisasiNamaKapital($data[$key] ?? '');
        }
    }
}

const NAMA_KAPITAL_FIELDS_STEP1 = ['nama_lengkap'];
const NAMA_KAPITAL_FIELDS_STEP2 = ['nama_ayah', 'nama_ibu'];

/** @param array<string, string> $data */
function normalisasiDataTk(array &$data): void
{
    $pernah = trim($data['pernah_tk'] ?? '');
    if (!in_array($pernah, ['Ya', 'Tidak'], true)) {
        $data['pernah_tk'] = '';
        $data['nama_tk'] = '';

        return;
    }

    $data['pernah_tk'] = $pernah;
    if ($pernah === 'Ya') {
        $data['nama_tk'] = normalisasiNamaKapital($data['nama_tk'] ?? '');
    } else {
        $data['nama_tk'] = '';
    }
}

function validasiDataTk(array $data, bool $wajibNamaTk = true): ?string
{
    if (($data['pernah_tk'] ?? '') === '') {
        return 'Pilih apakah siswa pernah TK atau belum.';
    }

    if ($wajibNamaTk && ($data['pernah_tk'] ?? '') === 'Ya' && ($data['nama_tk'] ?? '') === '') {
        return 'Nama TK wajib diisi jika memilih Ya.';
    }

    return null;
}

function formOld(array $data, string $key, string $default = ''): string
{
    return htmlspecialchars($data[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

function formChecked(array $data, string $key, string $value): string
{
    return (($data[$key] ?? '') === $value) ? 'checked' : '';
}

function formSelected(array $data, string $key, string $value): string
{
    return (($data[$key] ?? '') === $value) ? 'selected' : '';
}

function uploadBerkas(array $file, string $prefix): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return false;
    }

    $uploadDir = dirname(__DIR__) . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $filename = $prefix . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $target = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return 'uploads/' . $filename;
    }

    return false;
}

function sanitizePost(array $keys): array
{
    $out = [];
    foreach ($keys as $key) {
        $out[$key] = trim($_POST[$key] ?? '');
    }
    return $out;
}

function stmtBindParams(mysqli_stmt $stmt, array $params): void
{
    $types = str_repeat('s', count($params));
    $bind = [$types];
    foreach ($params as $key => $value) {
        $bind[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind);
}

/** @return array{hari: string, bulan: string, tahun: string} */
function splitTanggalPendaftaran(string $tanggal): array
{
    require_once __DIR__ . '/helpers_date.php';
    if ($tanggal === '') return ['hari' => '', 'bulan' => '', 'tahun' => ''];
    // If already ISO
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tanggal, $m)) {
        return ['hari' => $m[3], 'bulan' => $m[2], 'tahun' => $m[1]];
    }
    // Try parse Indonesian format
    $iso = parse_date_indonesia($tanggal);
    if ($iso && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $iso, $m2)) {
        return ['hari' => $m2[3], 'bulan' => $m2[2], 'tahun' => $m2[1]];
    }
    return ['hari' => '', 'bulan' => '', 'tahun' => ''];
}

function parseTanggalPendaftaranDariPost(): array
{
    // Support single input 'tanggal_pendaftaran' in Indonesian format (e.g. "07 Juni 2026")
    $raw = trim($_POST['tanggal_pendaftaran'] ?? '');
    if ($raw !== '') {
        require_once __DIR__ . '/helpers_date.php';
        $iso = parse_date_indonesia($raw);
        if ($iso) {
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $iso, $m)) {
                return ['hari' => $m[3], 'bulan' => $m[2], 'tahun' => $m[1]];
            }
        }
    }

    return [
        'hari' => preg_replace('/\D/', '', $_POST['tgl_hari'] ?? ''),
        'bulan' => preg_replace('/\D/', '', $_POST['tgl_bulan'] ?? ''),
        'tahun' => preg_replace('/\D/', '', $_POST['tgl_tahun'] ?? ''),
    ];
}

function validasiTanggalPendaftaran(array $parts): ?string
{
    $hari = $parts['hari'] ?? '';
    $bulan = $parts['bulan'] ?? '';
    $tahun = $parts['tahun'] ?? '';

    if ($hari === '' || $bulan === '' || $tahun === '') {
        return null;
    }

    if (strlen($tahun) !== 4) {
        return null;
    }

    $h = (int) $hari;
    $b = (int) $bulan;
    $t = (int) $tahun;

    if (!checkdate($b, $h, $t)) {
        return null;
    }

    return sprintf('%04d-%02d-%02d', $t, $b, $h);
}
