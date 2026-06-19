<?php

require_once __DIR__ . '/role.php';

/** Halaman yang boleh diakses petugas tanpa absen hari ini */
const ABSENSI_HALAMAN_IZIN = [
    'absen.php',
    'absen_proses.php',
    'proses_absen.php',
    'absen_verifikasi.php',
    // reference registration removed
    'logout.php',
];

function absensiKoneksi(): mysqli
{
    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        return $conn;
    }

    require dirname(__DIR__) . '/koneksi.php';
    require_once __DIR__ . '/ensure_absensi_schema.php';
    ensureAbsensiSchema($conn);

    return $conn;
}

function tanggalAbsensiHariIni(): string
{
    return date('Y-m-d');
}

function petugasSudahHadirHariIni(mysqli $conn, ?string $username = null): bool
{
    // Jika bukan petugas atau admin, anggap tidak perlu absen (boleh akses bebas)
    if (!isPetugas() && !isAdmin()) {
        return true;
    }

    $username = $username ?? ($_SESSION['username'] ?? '');
    if ($username === '') {
        return false;
    }

    $tanggal = tanggalAbsensiHariIni();
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id FROM daftar_hadir WHERE username = ? AND tanggal = ? AND status = 'hadir' LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, 'ss', $username, $tanggal);
    mysqli_stmt_execute($stmt);
    $ada = (bool) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($ada) {
        $_SESSION['absensi_hadir_tanggal'] = $tanggal;
    }

    return $ada;
}

function petugasBolehAksesMenu(): bool
{
    if (!isPetugas()) {
        return true;
    }

    if (($_SESSION['absensi_hadir_tanggal'] ?? '') === tanggalAbsensiHariIni()) {
        return true;
    }

    $conn = absensiKoneksi();

    return petugasSudahHadirHariIni($conn);
}

function wajibAbsensiPetugas(): void
{
    if (!isPetugas()) {
        return;
    }

    $halaman = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if (in_array($halaman, ABSENSI_HALAMAN_IZIN, true)) {
        return;
    }

    $conn = absensiKoneksi();

    if (petugasSudahHadirHariIni($conn)) {
        return;
    }

    header('Location: absen.php');
    exit;
}

/**
 * Catat kehadiran ke daftar_hadir
 *
 * @return array{ok: bool, pesan: string}
 */
function catatAbsensiHadir(mysqli $conn, string $username, string $namaPetugas, string $metode = 'wajah', ?string $fotoKehadiran = null): array
{
    if ($username === '') {
        return ['ok' => false, 'pesan' => 'Sesi login tidak valid.'];
    }

    if (petugasSudahHadirHariIni($conn, $username)) {
        $_SESSION['absensi_hadir_tanggal'] = tanggalAbsensiHariIni();

        return ['ok' => false, 'pesan' => 'Mohon maaf anda telah melakukan absensi sebelum nya dan telah dicatat oleh panitia. Silahkan lanjutkan untuk bekerja', 'sudah_absen' => true];
    }

    $tanggal = tanggalAbsensiHariIni();
    if ($fotoKehadiran !== null) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO daftar_hadir (username, nama_petugas, tanggal, status, waktu_masuk, metode_verifikasi, foto_kehadiran)
             VALUES (?, ?, ?, 'hadir', NOW(), ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'sssss', $username, $namaPetugas, $tanggal, $metode, $fotoKehadiran);
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO daftar_hadir (username, nama_petugas, tanggal, status, waktu_masuk, metode_verifikasi)
             VALUES (?, ?, ?, 'hadir', NOW(), ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $namaPetugas, $tanggal, $metode);
    }

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        return ['ok' => false, 'pesan' => 'Gagal menyimpan ke daftar_hadir: ' . mysqli_error($conn)];
    }

    mysqli_stmt_close($stmt);
    $_SESSION['absensi_hadir_tanggal'] = $tanggal;

    // Jika ada foto kehadiran, simpan juga sebagai `foto_petugas` pada tabel users (profil terakhir)
    if ($fotoKehadiran !== null) {
        $uStmt = mysqli_prepare($conn, "UPDATE users SET foto_petugas = ? WHERE username = ? LIMIT 1");
        if ($uStmt) {
            mysqli_stmt_bind_param($uStmt, 'ss', $fotoKehadiran, $username);
            mysqli_stmt_execute($uStmt);
            mysqli_stmt_close($uStmt);
        }
    }

    return ['ok' => true, 'pesan' => 'Absensi berhasil. Terima kasih atas kehadiran Anda.'];
}

/**
 * @return array<int, array{username: string, nama_petugas: string, waktu_masuk: string}>
 */
function daftarPetugasHadirTanggal(mysqli $conn, string $tanggal): array
{
    $stmt = mysqli_prepare(
        $conn,
        "SELECT username, nama_petugas, waktu_masuk FROM daftar_hadir
         WHERE tanggal = ? AND status = 'hadir' ORDER BY waktu_masuk ASC"
    );
    mysqli_stmt_bind_param($stmt, 's', $tanggal);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);

    return $rows;
}
